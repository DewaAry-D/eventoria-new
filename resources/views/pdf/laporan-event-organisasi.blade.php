<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Event - {{ $organisasi->nama_organisasi }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
        }
        .header {
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }
        .header p {
            margin: 0;
            font-size: 9px;
            color: #555555;
        }
        table.summary {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        table.summary td {
            width: 25%;
            padding: 8px 10px;
            border: 1px solid #dddddd;
            text-align: center;
        }
        table.summary .label {
            display: block;
            font-size: 8px;
            text-transform: uppercase;
            color: #666666;
            margin-bottom: 2px;
        }
        table.summary .value {
            display: block;
            font-size: 14px;
            font-weight: bold;
        }
        table.report {
            width: 100%;
            border-collapse: collapse;
        }
        table.report th {
            background-color: #2c3e50;
            color: #ffffff;
            text-align: left;
            padding: 6px 5px;
            font-size: 8.5px;
            text-transform: uppercase;
        }
        table.report td {
            padding: 5px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        table.report tr.even {
            background-color: #f7f7f7;
        }
        .status {
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: -15px;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Lengkap Event — {{ $organisasi->nama_organisasi }}</h1>
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WITA</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Event</span>
                <span class="value">{{ $totalEvent }}</span>
            </td>
            <td>
                <span class="label">Total Pendaftar</span>
                <span class="value">{{ $totalPendaftar }}</span>
            </td>
            <td>
                <span class="label">Menunggu ACC</span>
                <span class="value">{{ $menungguPersetujuan }}</span>
            </td>
            <td>
                <span class="label">Butuh Revisi</span>
                <span class="value">{{ $butuhRevisi }}</span>
            </td>
        </tr>
    </table>

    <table class="report">  
        <thead>
            <tr>
                <th style="width: 16%;">Nama Event</th>
                <th style="width: 9%;">Status DPM</th>
                <th style="width: 9%;">Kategori</th>
                <th style="width: 8%;">Tingkat</th>
                <th style="width: 7%;">Kuota</th>
                <th style="width: 7%;">Pendaftar</th>
                <th style="width: 7%;">Sisa Kuota</th>
                <th style="width: 15%;">Catatan Revisi</th>
                <th style="width: 12%;">Lokasi URL</th>
                <th style="width: 10%;">Narasumber</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $i => $event)
                <tr class="{{ $i % 2 === 1 ? 'even' : '' }}">
                    <td>{{ $event->nama_event }}</td>
                    <td class="status">{{ strtoupper($event->status->value) }}</td>
                    <td>{{ $event->kategori->nama_kategori ?? 'Umum' }}</td>
                    <td>{{ ucfirst($event->tingkat_event->value) }}</td>
                    <td>{{ $event->kuota ?? 'Tak Terbatas' }}</td>
                    <td>{{ $event->registrations_count }}</td>
                    <td>{{ $event->sisa_kuota }}</td>
                    <td>{{ $event->catatan_revisi ?? '-' }}</td>
                    <td>{{ $event->lokasi_url ?? '-' }}</td>
                    <td>{{ $event->narasumber ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding: 16px;">Belum ada event yang dibuat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan digenerate otomatis oleh sistem Eventoria — {{ $organisasi->nama_organisasi }}
    </div>
</body>
</html>