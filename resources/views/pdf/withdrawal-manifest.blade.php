<!DOCTYPE html>
<html>
<head>
    <title>Daftar Transfer Penarikan Bonus</title>
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
        <h2>DAFTAR TRANSFER PENARIKAN BONUS</h2>
        <p>Tanggal Cetak: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Member</th>
                <th width="15%">Bank</th>
                <th width="20%">Nomor Rekening</th>
                <th width="20%">Atas Nama (Rekening)</th>
                <th width="20%" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($data as $index => $row)
                @php $grandTotal += $row['nominal']; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['bank_name'] ?? '-' }}</td>
                    <td>{{ $row['account_number'] ?? '-' }}</td>
                    <td>{{ $row['account_name'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row['nominal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="5" class="text-right">TOTAL TRANSFER</td>
                <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>