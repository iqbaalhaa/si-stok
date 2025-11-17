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
                <th>Motor</th>
                <th>Harga Cash</th>
                <th>Uang Muka</th>
                <th>Angsuran/Bulan</th>
                <th>Lama Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr>
                <td>{{ $r->motor->nama_motor ?? '-' }}</td>
                <td>{{ number_format($r->harga_cash,0,',','.') }}</td>
                <td>{{ number_format($r->uang_muka,0,',','.') }}</td>
                <td>{{ number_format($r->angsuran,0,',','.') }}</td>
                <td>{{ $r->lama_kredit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
 </body>
 </html>