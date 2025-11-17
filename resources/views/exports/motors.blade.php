<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 16px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
    <title>{{ $title ?? 'Laporan' }}</title>
 </head>
 <body>
    <h1>{{ $title ?? 'Laporan' }}</h1>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Warna</th>
                <th>Tahun</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr>
                <td>{{ $r->kode_motor }}</td>
                <td>{{ $r->nama_motor }}</td>
                <td>{{ $r->tipe }}</td>
                <td>{{ $r->warna }}</td>
                <td>{{ $r->tahun }}</td>
                <td>{{ number_format($r->harga_jual,0,',','.') }}</td>
                <td>{{ $r->stok }}</td>
                <td>{{ $r->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
 </body>
 </html>