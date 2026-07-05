<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
            background-color: #ffffff;
        }

        /* Gambar template mengisi penuh halaman PDF tanpa ada overlay nama */
        .template-bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
        }

        .template-bg img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .nama-cetak {
            position: absolute;
            z-index: 2;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            
            top: {{ $posisiY }}%;
            
            /* Properti Font */
            font-family: {{ $jenisFont }}, Arial, sans-serif;
            font-size: {{ $ukuranFont }}px;
            color: {{ $warnaFont }};
            font-weight: bold;
        }
    </style>
</head>
<body>
    {{-- Hanya gambar template dari organisasi, tidak ada nama yang di-overlay --}}
    {{-- Nama sudah tercetak langsung di dalam gambar template oleh organisasi --}}
    @if($imageBase64)
        <div class="template-bg">
            <img src="{{ $imageBase64 }}" alt="Sertifikat">
        </div>
    @else
        {{-- Fallback jika file gambar di server mengalami korup/hilang --}}
        <div style="width: 100%; height: 100%; background: #f8f8f8;"></div>
    @endif

    <div class="nama-cetak">
        {{ $namaCetak }}
    </div>
</body>
</html>