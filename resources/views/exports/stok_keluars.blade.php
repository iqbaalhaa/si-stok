<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 16px; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        thead th { background: #e30613; color: #fff; }
        tbody tr:nth-child(even) { background: #f7f7f7; }
        .letterhead { display: flex; align-items: center; gap: 14px; }
        .letterhead .logo { height: 60px; }
        .letterhead .info { line-height: 1.25; }
        .letterhead .name { font-size: 18px; font-weight: bold; }
        .letterhead .meta { font-size: 12px; color: #444; }
        .divider { border-top: 2px solid #e30613; margin: 8px 0 12px; }
    </style>
    <title>{{ $title ?? 'Laporan' }}</title>
 </head>
 <body>
    @php($setting = \App\Models\SystemSetting::query()->first())
    @php($logo = $setting?->logo)
    @php($logoPath = $logo ? public_path('uploads/' . ltrim($logo, '/')) : null)
    <div class="letterhead">
        @if($logoPath)
            <img class="logo" src="{{ $logoPath }}" alt="Logo">
        @endif
        <div class="info">
            <div class="name">{{ $setting?->nama_perusahaan ?? ($setting?->nama_sistem ?? 'Perusahaan') }}</div>
            @if($setting?->alamat)<div class="meta">{{ $setting->alamat }}</div>@endif
            @if($setting?->telepon)<div class="meta">Telp: {{ $setting->telepon }}</div>@endif
        </div>
    </div>
    <div class="divider"></div>
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
