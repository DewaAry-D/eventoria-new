<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Kampus Overview</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header { margin-bottom: 15px; border-bottom: 2px solid #000666; padding-bottom: 8px; }
        .header h2 { margin: 0; color: #000666; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; color: #666; font-size: 11px; }
        .grid-stats { width: 100%; margin-bottom: 15px; }
        .card-stat { background: #f8f9fa; border: 1px solid #e9ecef; padding: 8px; border-radius: 6px; text-align: center; }
        .card-stat h4 { margin: 0; font-size: 9px; text-transform: uppercase; color: #6c757d; }
        .card-stat p { margin: 4px 0 0 0; font-size: 16px; font-weight: bold; color: #000666; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        .data-table th, .data-table td { border: 1px solid #dee2e6; padding: 6px 8px; text-align: left; vertical-align: top; word-wrap: break-word; }
        .data-table th { background-color: #000666; color: white; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .data-table tr:nth-child(even) { background-color: #f8f9fa; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Ikhtisar Aktivitas Manajemen Kampus</h2>
        <p>Wilayah Cakupan: {{ $scopeName }} | Diekstrak Pada: {{ now()->translatedFormat('d F Y H:i') }} WITA</p>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="grid-stats" cellspacing="5">
        <tr>
            <td width="25%"><div class="card-stat"><h4>Organisasi Aktif</h4><p>{{ $cardsData['orgAktif'] }}</p></div></td>
            <td width="25%"><div class="card-stat"><h4>Event Berhasil</h4><p>{{ $cardsData['eventBerlangsung'] }}</p></div></td>
            <td width="25%"><div class="card-stat"><h4>Review Event Baru</h4><p>{{ $statPengajuan['menunggu'] }}</p></div></td>
            <td width="25%"><div class="card-stat"><h4>Selesai / Publikasi</h4><p>{{ $statPengajuan['disetujui'] }}</p></div></td>
        </tr>
    </table>

    <!-- Tabel Master Data Master Antrean Lengkap -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="18%">Nama Event</th>
                <th width="12%">Penyelenggara</th>
                <th width="9%">Kategori</th>
                <th width="9%">Tingkat</th>
                <th width="8%">Status</th>
                <th width="6%" class="text-center">Kuota</th>
                <th width="6%" class="text-center">Sisa</th>
                <th width="12%">Narasumber</th>
                <th width="12%">Lokasi</th>
                <th width="8%">Tgl Diajukan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                @php
                    $status = is_object($event->status) ? ($event->status?->value ?? '-') : $event->status;
                    $tingkat = is_object($event->tingkat_event) ? ($event->tingkat_event?->value ?? '-') : $event->tingkat_event;
                @endphp
                <tr>
                    <td><strong>{{ $event->nama_event }}</strong></td>
                    <td>{{ $event->organisasi?->nama_organisasi ?? '-' }}</td>
                    <td>{{ $event->kategori?->nama_kategori ?? '-' }}</td>
                    <td>{{ strtoupper($tingkat ?? '-') }}</td>
                    <td>{{ strtoupper($status ?? '-') }}</td>
                    <td class="text-center">{{ $event->kuota ?? 0 }}</td>
                    <td class="text-center">{{ $event->sisa_kuota ?? 0 }}</td>
                    <td>{{ $event->narasumber ?? '-' }}</td>
                    <td>{{ $event->nama_lokasi ?? '-' }}</td>
                    <td>{{ $event->created_at ? $event->created_at->format('Y-m-d H:i') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>