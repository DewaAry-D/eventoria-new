<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Kampus Overview</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #000666; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #000666; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 4px 0 0 0; color: #666; font-size: 12px; }
        .grid-stats { width: 100%; margin-bottom: 20px; }
        .card-stat { background: #f8f9fa; border: 1px solid #e9ecef; padding: 10px; border-radius: 6px; text-align: center; }
        .card-stat h4 { margin: 0; font-size: 10px; text-transform: uppercase; color: #6c757d; }
        .card-stat p { margin: 5px 0 0 0; font-size: 18px; font-weight: bold; color: #000666; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th, .data-table td { border: 1px solid #dee2e6; padding: 7px 10px; text-align: left; }
        .data-table th { background-color: #000666; color: white; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .data-table tr:nth-child(even) { background-color: #f8f9fa; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Ikhtisar Aktivitas Manajemen Kampus</h2>
        <p>Wilayah Cakupan: {{ $scopeName }} | Diekstrak Pada: {{ now()->translatedFormat('d F Y H:i') }} WITA</p>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="grid-stats" cellspacing="10">
        <tr>
            <td width="25%"><div class="card-stat"><h4>Organisasi Aktif</h4><p>{{ $cardsData['orgAktif'] }}</p></div></td>
            <td width="25%"><div class="card-stat"><h4>Event Berhasil</h4><p>{{ $cardsData['eventBerlangsung'] }}</p></div></td>
            <td width="25%"><div class="card-stat"><h4>Review Event Baru</h4><p>{{ $statPengajuan['menunggu'] }}</p></div></td>
            <td width="25%"><div class="card-stat"><h4>Selesai / Publikasi</h4><p>{{ $statPengajuan['disetujui'] }}</p></div></td>
        </tr>
    </table>

    <!-- Tabel Master Data Master Antrean -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="35%">Nama Acara / Event</th>
                <th width="15%">Kategori</th>
                <th width="20%">Organisasi Penyelenggara</th>
                <th width="15%">Jangkauan Tingkat</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
                <tr>
                    <td>{{ $event->id }}</td>
                    <td><strong>{{ $event->nama_event }}</strong></td>
                    <td>{{ $event->kategori?->nama_kategori ?? '-' }}</td>
                    <td>{{ $event->organisasi?->nama_organisasi ?? '-' }}</td>
                    <td>{{ strtoupper($event->tingkat_event->value ?? $event->tingkat_event) }}</td>
                    <td>{{ strtoupper($event->status->value ?? $event->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>