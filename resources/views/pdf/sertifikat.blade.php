<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /*
         * Catatan penting untuk dompdf:
         * - Semua ukuran pakai satuan yang dompdf pahami (px, pt, mm)
         * - Tidak support flexbox/grid, pakai position: absolute
         * - A4 landscape = 297mm x 210mm = ~842px x 595px di 72dpi
         */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 842px;
            height: 595px;
            overflow: hidden;
            position: relative;
        }

        /* Gambar template sebagai background penuh */
        .template-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 842px;
            height: 595px;
            z-index: 1;
        }

        .template-bg img {
            width: 842px;
            height: 595px;
        }

        /*
         * Nama cetak diposisikan sesuai posisi_x dan posisi_y
         * yang diatur oleh panitia lewat sertifikat-builder.
         * Nilai x/y dari database adalah pixel dari pojok kiri-atas.
         */
        .nama-cetak {
            position: absolute;
            z-index: 2;
            text-align: center;
            width: 600px; /* lebar area teks, bisa disesuaikan */
            /* posisi horizontal: x dikurangi setengah lebar agar teks center di titik x */
            left: {{ $posisiX - 300 }}px;
            top: {{ $posisiY }}px;
            font-family: {{ $jenisFont }}, Arial, sans-serif;
            font-size: {{ $ukuranFont }}px;
            color: {{ $warnaFont }};
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- Layer 1: Gambar template sertifikat sebagai background --}}
    <div class="template-bg">
        @if($imageBase64)
            <img src="{{ $imageBase64 }}" alt="Template Sertifikat">
        @else
            {{-- Fallback kalau gambar tidak ditemukan: background putih polos --}}
            <div style="width:842px; height:595px; background:#f8f8f8; border: 1px solid #ddd;"></div>
        @endif
    </div>

    {{-- Layer 2: Nama penerima sertifikat, diposisikan sesuai data dari panitia --}}
    <div class="nama-cetak">
        {{ $namaCetak }}
    </div>

</body>
</html>