<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container {
            width: 100%;
            text-align: center;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
        }

        .items {
            text-align: left;
            width: 100%;
            margin-top: 10px;
        }

        .items th,
        .items td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <p class="header">Struk Pembelian</p>
        <p><strong>Nama Pelanggan:</strong> {{ optional($transaction->customers)->name }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('j M Y') }}</p>
        <hr>
        <table class="items">
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
            @foreach($transaction->items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->pivot->quantity }}x</td>
                <td>Rp.{{ number_format($item->price, 2) }}</td>
                <td>Rp.{{ number_format($item->pivot->quantity * $item->price, 2) }}</td>
            </tr>
            @endforeach
        </table>
        <hr>
        <p><strong>Total:</strong> Rp.{{ number_format($transaction->total, 2) }}</p>
        <p>Terima kasih atas pembelian Anda!</p>
    </div>
</body>

</html>