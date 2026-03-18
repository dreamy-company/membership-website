<!DOCTYPE html>
<html>
<head>
    <title>Bukti Transfer Penarikan Bonus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #777;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .total {
            font-weight: bold;
            background-color: #e9e9e9;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>BUKTI TRANSFER PENARIKAN BONUS</h2>
        <p>Tanggal Cetak: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="2" style="text-align: center;">DETAIL PENARIKAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="30%"><strong>Tanggal Penarikan</strong></td>
                <td>{{ \Carbon\Carbon::parse($withdrawal->date)->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Kode Member</strong></td>
                <td>{{ $withdrawal->member->member_code ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Nama Member</strong></td>
                <td>{{ $withdrawal->member->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Bank Tujuan</strong></td>
                <td>{{ $withdrawal->member->bank_name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Nomor Rekening</strong></td>
                <td>{{ $withdrawal->member->account_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Atas Nama (Rekening)</strong></td>
                <td>{{ $withdrawal->member->account_name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>Berhasil / Sukses</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total">
                <td class="text-right">TOTAL TRANSFER (Rp)</td>
                <td style="font-size: 14px;">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>