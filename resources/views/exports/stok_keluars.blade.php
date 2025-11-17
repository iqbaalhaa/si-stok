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
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr>
                <td>{{ $r->motor->nama_motor ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                <td>{{ $r->jumlah }}</td>
                <td>{{ $r->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
 </body>
 </html>