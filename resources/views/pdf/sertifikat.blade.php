<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        /* Gambar template mengisi penuh halaman PDF tanpa ada overlay nama */
        .template-bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
        }

        .template-bg img {
            width: 100%;
            height: 100%;
            display: block;
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
    @endif
</body>
</html>