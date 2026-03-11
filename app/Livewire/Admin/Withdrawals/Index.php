<?php

namespace App\Livewire\Admin\Withdrawals;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

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


    protected $queryString = ['search' => ['except' => '']];
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
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
       $withdrawals = Withdrawal::search($this->search)
                      ->latest()
                      ->paginate($this->perPage);

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

        // Kosongkan array saat modal dibuka
        $this->withdrawalAmounts = []; 

        // 2. Hitung saldo dan siapkan default input
        $this->memberBalance = $members->map(function ($member) {
            // Simpan sebagai properti agar mudah dipanggil di Blade
            $member->total_bonus = $member->transactions_sum_bonus ?? 0;
            $member->total_ditarik = $member->withdrawals_sum_amount ?? 0;
            $member->sisa_saldo = $member->total_bonus - $member->total_ditarik;
            
            return $member;
        })->filter(function ($member) {
            return $member->sisa_saldo > 0;
        })->values();

        // 3. Set default 'Jumlah Penarikan' ke maksimal 'Sisa Saldo'
        foreach ($this->memberBalance as $member) {
            $this->withdrawalAmounts[$member->id] = $member->sisa_saldo;
        }

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
            
            $validMembers = []; // Untuk menyimpan sementara member yang saldonya > 0
            $withdrawalData = []; // Untuk data tabel di PDF

            // FASE 1: Kalkulasi Saldo & Kumpulkan Data
            foreach ($members as $member) {
                $totalBonus = Transaction::where('member_id', $member->id)->sum('bonus');
                $totalDitarik = Withdrawal::where('member_id', $member->id)->sum('amount');
                
                $sisaSaldo = $totalBonus - $totalDitarik;

                if ($sisaSaldo > 0) {
                    // Simpan data untuk diproses nanti
                    $validMembers[] = [
                        'member_id' => $member->id,
                        'amount'    => $sisaSaldo,
                    ];

                    // Simpan data untuk PDF
                    $withdrawalData[] = [
                        'nama'           => $member->user->name,
                        'bank_name'      => $member->bank_name,
                        'account_number' => $member->account_number,
                        'account_name'   => $member->account_name,
                        'nominal'        => $sisaSaldo,
                    ];
                }
            }

            // Jika ternyata semuanya sudah ditarik (saldo 0)
            if (count($validMembers) === 0) {
                $this->dispatch('error', ['message' => 'Semua member yang dipilih saldonya sudah Rp 0.']);
                return;
            }

            // FASE 2: Generate & Simpan PDF ke Storage
            $fileName = 'Manifest_Penarikan_' . time() . '.pdf';
            $filePath = 'withdrawals/' . $fileName; // Path: storage/app/public/withdrawals/...

            $pdf = Pdf::loadView('pdf.withdrawal-manifest', [
                'data' => $withdrawalData,
                'tanggal' => now()->format('d F Y H:i')
            ]);

            // Simpan PDF ke dalam folder storage public
            Storage::disk('public')->put($filePath, $pdf->output());

            // FASE 3: Simpan ke Database
            foreach ($validMembers as $item) {
                Withdrawal::create([
                    'member_id'       => $item['member_id'],
                    'amount'          => $item['amount'],
                    'date'            => now(),
                    'payment_receipt' => $filePath, // <<--- SIMPAN PATH PDF DI SINI
                ]);
            }

            DB::commit();

            // Reset UI
            $this->selectedMembers = [];
            $this->isOpen = false;

            $this->dispatch('success', ['message' => 'Penarikan berhasil diproses & PDF disimpan!']);

            // FASE 4 (Opsional): Tetap mendownload PDF ke browser Admin sebagai arsip langsung
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
            }, $fileName);

        } catch (\Exception $e) {
            DB::rollBack();
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

}
