<?php

namespace App\Livewire\Admin\Withdrawals;

use App\Models\Member;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

use function Symfony\Component\Clock\now;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $withdrawal_id;
    public $member_id;
    public $amount;
    public $date;
    public $payment_receipt;
    public $members;
    public $isOpen = false;
    public $confirmingDelete;
    public $perPage = 10;
    public $title = "Withdrawal Management";
    public $oldImage;
    public $image;
    public $memberBalance = [];
    public $selectedMembers = [];
    public $withdrawalAmounts = [];

    public $available_balance = 0;

    public $old_payment_receipt;

    // Variabel Filter
    public $start_date;
    public $end_date;


    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    // Mengganti updatingSearch menjadi updated agar berlaku untuk semua filter
    public function updated($property)
    {
        if (in_array($property, ['search', 'start_date', 'end_date'])) {
            $this->resetPage();
        }
    }

    public function mount($withdrawal = null)
    {
        $this->members = Member::with('user')->get();
        if ($withdrawal) {
            $this->old_payment_receipt = $withdrawal->payment_receipt ? asset('storage/' . $withdrawal->payment_receipt) : null;
        }
    }

    public function render()
    {
        // 1. Mulai dengan Query Pencarian Bawaan
        $query = Withdrawal::search($this->search);

        // 2. Tambahkan Logika Filter Tanggal
        if ($this->start_date && $this->end_date) {
            $query->whereBetween('date', [$this->start_date, $this->end_date]);
        } elseif ($this->start_date) {
            $query->whereDate('date', '>=', $this->start_date);
        } elseif ($this->end_date) {
            $query->whereDate('date', '<=', $this->end_date);
        }

        // 3. Eksekusi Query
        $withdrawals = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.withdrawals.index', compact('withdrawals'));
    }

    public function updatedMemberId($value)
    {
        // Reset ke 0 dulu
        $this->available_balance = 0; 

        if ($value) {
            $member = \App\Models\Member::find($value);
            
            if ($member) {
                // Kita panggil Virtual Attribute tadi
                // Laravel otomatis menjalankan logic: (Sum BonusLog) - (Sum Withdrawal)
                $this->available_balance = $member->balance;
            }
        }
    }

    public function openModal($id = null)
    {
        $this->resetInput();

        if ($id) {
            $withdrawal = Withdrawal::findOrFail($id);
            $this->withdrawal_id = $withdrawal->id;
            $this->member_id = $withdrawal->member_id;
            $this->amount = $withdrawal->amount;
            $this->date = $withdrawal->date;
            $this->old_payment_receipt = $withdrawal->payment_receipt;
        }

        // 1. Ambil data member beserta total bonus dan total penarikannya
        $members = Member::with('user')
            ->withSum('transactions', 'bonus')
            ->withSum('withdrawals', 'amount')
            ->get();

        $this->withdrawalAmounts = []; 

        // Filter member yang hanya punya sisa saldo > 0
        $this->memberBalance = $members->map(function ($member) {
            $totalBonus = $member->transactions_sum_bonus ?? 0;
            $totalDitarik = $member->withdrawals_sum_amount ?? 0;
            
            return [
                'id'            => $member->id,
                'name'          => $member->user->name ?? '-',
                'total_bonus'   => $totalBonus,
                'total_ditarik' => $totalDitarik,
                'sisa_saldo'    => $totalBonus - $totalDitarik,
            ];
        })->filter(function ($item) {
            return $item['sisa_saldo'] > 0;
        })->values()->toArray(); 

        // Update loop default nilainya
        foreach ($this->memberBalance as $item) {
            $this->withdrawalAmounts[$item['id']] = $item['sisa_saldo'];
        }
        $this->selectedMembers = array_column($this->memberBalance, 'id');

        $this->isOpen = true;
    }


    public function closeModal()
    {
       
        $this->isOpen = false;
    }

   private function resetInput()
    {
        $this->amount = '';
        $this->date = '';
        $this->payment_receipt = null;
        $this->old_payment_receipt = null;
        $this->member_id = null;
        $this->available_balance = 0;
        $this->withdrawal_id = null;
    }

    public function store()
    {
        // 1. Validasi
        if (empty($this->selectedMembers)) {
            $this->dispatch('error', ['message' => 'Pilih minimal satu member untuk ditarik!']);
            return;
        }

        try {
            DB::beginTransaction();

            $members = Member::with('user')->whereIn('id', $this->selectedMembers)->get();
            
            $validMembers = []; 
            $withdrawalData = []; 

            // FASE 1: Kalkulasi Saldo & Kumpulkan Data
            foreach ($members as $member) {
                $totalBonus = Transaction::where('member_id', $member->id)->sum('bonus');
                $totalDitarik = Withdrawal::where('member_id', $member->id)->sum('amount');
                
                $sisaSaldo = $totalBonus - $totalDitarik;

                if ($sisaSaldo > 0) {
                    $validMembers[] = [
                        'member_id' => $member->id,
                        'amount'    => $sisaSaldo,
                    ];

                    $withdrawalData[] = [
                        'nama'           => $member->user->name,
                        'bank_name'      => $member->bank_name ?? 'LAINNYA / KOSONG',
                        'account_number' => $member->account_number,
                        'account_name'   => $member->account_name ?? $member->user->name,
                        'nominal'        => $sisaSaldo,
                    ];
                }
            }

            if (count($validMembers) === 0) {
                $this->dispatch('error', ['message' => 'Semua member yang dipilih saldonya sudah Rp 0.']);
                return;
            }

            // =========================================================
            // KUNCI PERBAIKAN: GROUPING DATA PER BANK
            // =========================================================
            $groupedMembers = collect($withdrawalData)->groupBy('bank_name')->sortKeys();

            // FASE 2: Generate & Simpan PDF ke Storage
            $fileName = 'Manifest_Penarikan_' . time() . '.pdf';
            $filePath = 'withdrawals/' . $fileName; 

            $pdf = Pdf::loadView('pdf.withdrawal-manifest', [
                'groupedData' => $groupedMembers, // <-- INI YANG MEMPERBAIKI ERROR!
                'tanggal'     => now()->format('d F Y H:i')
            ]);

            Storage::disk('public')->put($filePath, $pdf->output());

            // FASE 3: Simpan ke Database
            foreach ($validMembers as $item) {
                Withdrawal::create([
                    'member_id'       => $item['member_id'],
                    'amount'          => $item['amount'],
                    'date'            => now(),
                    'payment_receipt' => $filePath, 
                ]);
            }

            DB::commit();

            // =========================================================
            // FASE 4: GENERATE EXCEL (GROUP PER BANK) UNTUK DOWNLOAD
            // =========================================================
            $exportData = [];
            $grandTotal = 0;

            foreach ($groupedMembers as $bank => $membersInBank) {
                // Baris Header Bank di Excel
                $exportData[] = [
                    'No'                   => '',
                    'Nama Member'          => '',
                    'Bank'                 => 'BANK: ' . strtoupper($bank),
                    'Nomor Rekening'       => '',
                    'Atas Nama (Rekening)' => '',
                    'Nominal (Rp)'         => '',
                ];

                $no = 1;
                foreach ($membersInBank as $item) {
                    $exportData[] = [
                        'No'                   => $no++,
                        'Nama Member'          => $item['nama'],
                        'Bank'                 => $item['bank_name'],
                        'Nomor Rekening'       => " " . ($item['account_number'] ?? '-'),
                        'Atas Nama (Rekening)' => $item['account_name'] ?? '-',
                        'Nominal (Rp)'         => $item['nominal'],
                    ];
                    $grandTotal += $item['nominal'];
                }
                // Spasi kosong antar bank
                $exportData[] = ['', '', '', '', '', '']; 
            }

            // Baris Grand Total di bawah
            $exportData[] = [
                'No'                   => '',
                'Nama Member'          => '',
                'Bank'                 => '',
                'Nomor Rekening'       => '',
                'Atas Nama (Rekening)' => 'TOTAL KESELURUHAN',
                'Nominal (Rp)'         => $grandTotal,
            ];

            $fileNameExcel = 'Daftar_Transfer_Bonus_' . time() . '.xlsx';
            
            $exportClass = new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                protected $data;
                public function __construct(array $data) { $this->data = $data; }
                public function array(): array { return $this->data; }
                public function headings(): array {
                    return ['No', 'Nama Member', 'Bank', 'Nomor Rekening', 'Atas Nama (Rekening)', 'Nominal (Rp)'];
                }
            };

            // Reset UI
            $this->selectedMembers = [];
            $this->isOpen = false;
            $this->dispatch('success', ['message' => 'Penarikan berhasil! PDF tersimpan & Excel sedang diunduh.']);

            // Mengembalikan file EXCEL untuk diunduh otomatis
            return \Maatwebsite\Excel\Facades\Excel::download($exportClass, $fileNameExcel);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error agar mudah dilacak jika terjadi masalah lain
            \Illuminate\Support\Facades\Log::error('Withdrawal Store Error: ' . $e->getMessage());
            $this->dispatch('error', ['message' => 'Gagal memproses penarikan: ' . $e->getMessage()]);
        }
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        $withdrawal = Withdrawal::findOrFail($this->confirmingDelete);

        // Hapus file payment_receipt jika ada
        if ($withdrawal->payment_receipt && Storage::disk('public')->exists($withdrawal->payment_receipt)) {
            Storage::disk('public')->delete($withdrawal->payment_receipt);
        }

        // Hapus record
        $withdrawal->delete();

        $this->dispatch('success', [
            'type' => 'success',
            'message' => 'Withdrawal berhasil dihapus!',
        ]);
    }

    protected function rules()
    {
        return [
            'amount'    => 'required|numeric',
            'member_id' => 'required|integer|exists:members,id',
            'date' => 'required|date',
            'payment_receipt'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:1024',
        ];
    }

    protected function formData()
    {
        return [
            'amount'    => $this->amount,
            'member_id' => $this->member_id,
            'date' => $this->date,
            'payment_receipt'   => $this->payment_receipt,
        ];
    }

    protected function afterSave($created)
    {
        $this->closeModal();
        $this->resetInput();

        $message = $created
            ? 'Withdrawal berhasil ditambahkan!'
            : 'Withdrawal berhasil diupdate!';

        $this->dispatch('success', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    // ==========================================
    // FITUR EXPORT EXCEL SELURUH DATA / FILTER
    // ==========================================
    public function exportExcelAll()
    {
        $query = Withdrawal::search($this->search);

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('date', [$this->start_date, $this->end_date]);
        } elseif ($this->start_date) {
            $query->whereDate('date', '>=', $this->start_date);
        } elseif ($this->end_date) {
            $query->whereDate('date', '<=', $this->end_date);
        }

        $withdrawals = $query->with('member.user')->latest()->get();

        if ($withdrawals->isEmpty()) {
            $this->dispatch('error', ['message' => 'Tidak ada data untuk di-export pada rentang waktu ini.']);
            return;
        }

        // Grouping berdasarkan Bank
        $groupedWithdrawals = $withdrawals->groupBy(function($item) {
            return $item->member->bank_name ?? 'LAINNYA / KOSONG';
        })->sortKeys();

        $exportData = [];
        $grandTotal = 0;

        foreach ($groupedWithdrawals as $bank => $membersInBank) {
            // Baris Header Bank
            $exportData[] = [
                'No'                   => '',
                'Nama Member'          => '',
                'Bank'                 => 'BANK: ' . strtoupper($bank),
                'Nomor Rekening'       => '',
                'Atas Nama (Rekening)' => '',
                'Nominal (Rp)'         => '',
            ];

            $no = 1;
            foreach ($membersInBank as $item) {
                $exportData[] = [
                    'No'                   => $no++,
                    'Nama Member'          => $item->member->user->name ?? '-',
                    'Bank'                 => $item->member->bank_name ?? '-',
                    'Nomor Rekening'       => " " . ($item->member->account_number ?? '-'),
                    'Atas Nama (Rekening)' => $item->member->account_name ?? '-',
                    'Nominal (Rp)'         => $item->amount,
                ];
                $grandTotal += $item->amount;
            }
            $exportData[] = ['', '', '', '', '', ''];
        }

        // Baris Grand Total
        $exportData[] = [
            'No'                   => '',
            'Nama Member'          => '',
            'Bank'                 => '',
            'Nomor Rekening'       => '',
            'Atas Nama (Rekening)' => 'TOTAL KESELURUHAN',
            'Nominal (Rp)'         => $grandTotal,
        ];

        $fileName = 'Laporan_Withdrawal_' . now()->format('Ymd_His') . '.xlsx';
        
        $exportClass = new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array {
                return ['No', 'Nama Member', 'Bank', 'Nomor Rekening', 'Atas Nama (Rekening)', 'Nominal (Rp)'];
            }
        };

        return Excel::download($exportClass, $fileName);
    }

}