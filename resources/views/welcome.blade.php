<?php
// Eventoria - Campus Event Management Platform
// Mengambil data riil dari Database menggunakan Eloquent

// 1. Ambil data count untuk Statistik (dengan fallback angka dummy jika database masih kosong saat dev)
$dbEventCount = \App\Models\Event::where('status', 'published')->count();
$dbOrgCount = \App\Models\OrganisasiMahasiswa::where('status', 'approved')->count();
$dbMhsCount = \App\Models\Mahasiswa::count();

$stats = [
    [
        'value' => $dbEventCount > 0 ? number_format($dbEventCount, 0, ',', '.') . '+' : '1.200+', 
        'label' => 'Total Event'
    ],
    [
        'value' => $dbOrgCount > 0 ? $dbOrgCount . '+' : '85+', 
        'label' => 'Total Organisasi'
    ],
    [
        'value' => $dbMhsCount > 0 ? number_format($dbMhsCount, 0, ',', '.') . '+' : '10k+', 
        'label' => 'Total Mahasiswa'
    ],
];

// 2. Ambil Kategori Populer dari Database
$categories = \App\Models\Kategori::take(4)->get();

// 3. Ambil 3 Event Terbaru yang Berstatus Published
$events = \App\Models\Event::with(['organisasi', 'timeLines'])
    ->where('status', 'published')
    ->latest()
    ->take(3)
    ->get();

$footer_links = [
    'LAYANAN'     => ['Cari Event', 'Pendaftaran Organisasi', 'Sistem Penilaian'],
    'ORGANISASI'  => ['Tentang Kami', 'Kontak', 'Panduan'],
    'BANTUAN'     => ['FAQ', 'Pusat Bantuan', 'Kebijakan Privasi'],
];
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventoria – Temukan Event Campus Terbaik</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-background text-on-surface-variant antialiased text-[15px] leading-relaxed">

<header class="sticky top-0 z-[100] bg-surface-container-lowest border-b border-outline-variant py-3.5">
    <div class="max-w-container mx-auto px-6 w-full">
        <nav class="flex items-center justify-between gap-4">
            <a href="{{ route('login') }}" class="text-xl font-extrabold text-primary tracking-tight justify-left">Eventoria</a>

            <ul class="hidden md:flex gap-8 list-none">
                <li><a href="{{ route('login') }}" class="font-medium text-on-surface-variant text-sm pb-0.5 border-b-2 border-transparent hover:text-primary hover:border-primary transition-colors">Login</a></li>
            </ul>
        </nav>
    </div>
    
    <div class="hidden flex-col bg-surface-container-lowest border-t border-outline-variant px-6 py-4 gap-3" id="mobileMenu">
        <a href="{{ route('login') }}" class="font-medium text-on-surface py-2 border-b border-outline-variant">Dashboard</a>
        <a href="{{ route('login') }}" class="font-medium text-on-surface py-2 border-b border-outline-variant">Events</a>
        <a href="{{ route('login') }}" class="font-medium text-on-surface py-2 border-b border-outline-variant">Login</a>
    </div>
</header>

<section class="bg-surface-container-low py-16 md:py-20 text-center">
    <div class="max-w-container mx-auto px-6 w-full">
        <span class="inline-block text-xs font-bold tracking-[1.5px] uppercase text-primary-container mb-5">Platform Event Kampus #1</span>
        <h1 class="text-headline-lg-mobile md:text-headline-lg font-black md:font-black text-primary max-w-[700px] mx-auto mb-5 tracking-tight">Temukan Event Campus Terbaik dengan Mudah</h1>
        <p class="max-w-[520px] mx-auto text-on-surface-variant text-body-lg mb-9">Platform manajemen event modern untuk mahasiswa dan organisasi kampus. Kelola, cari, dan ikuti berbagai kegiatan akademik serta kreatif dalam satu tempat.</p>

        <div class="flex flex-wrap justify-center gap-3.5 mb-10">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded text-on-primary bg-primary border-2 border-primary hover:bg-primary-container transition-all font-semibold text-[15px] hover:-translate-y-[1px]">
                <i class="fa-solid fa-compass"></i> Jelajahi Event
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded text-primary bg-transparent border-2 border-primary hover:bg-primary hover:text-on-primary transition-all font-semibold text-[15px] hover:-translate-y-[1px]">
                <i class="fa-solid fa-building-columns"></i> Daftar Organisasi
            </a>
        </div>

        <div class="flex items-center max-w-[560px] mx-auto bg-surface-container-lowest border-[1.5px] border-outline-variant rounded-md p-1.5 pl-4 shadow-sm gap-2.5">
            <i class="fa-solid fa-magnifying-glass text-outline shrink-0"></i>
            <input type="text" placeholder="Cari nama event, organisasi, atau topik..." class="flex-1 border-none outline-none font-sans text-sm text-on-surface bg-transparent min-w-0 placeholder-outline focus:ring-0">
            <a href="{{ route('login') }}" class="flex items-center justify-center bg-primary text-on-primary border-none rounded-[7px] px-5 py-2.5 font-semibold text-sm cursor-pointer whitespace-nowrap hover:bg-primary-container transition-colors">Cari Sekarang</a>
        </div>
    </div>
</section>

<section class="border-y border-outline-variant py-12">
    <div class="max-w-container mx-auto px-6 w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:divide-x divide-outline-variant">
            <?php foreach ($stats as $s): ?>
            <div class="flex flex-col">
                <div class="text-[32px] md:text-[42px] font-black text-primary tracking-tight leading-none"><?= htmlspecialchars($s['value']) ?></div>
                <div class="text-xs uppercase tracking-[1px] text-outline font-semibold mt-2"><?= htmlspecialchars($s['label']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-container mx-auto px-6 w-full">
        <div class="flex items-end justify-between flex-wrap gap-3 mb-7">
            <h2 class="text-headline-md font-extrabold text-primary">Kategori Event Populer</h2>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($categories as $cat)
            <a href="{{ route('login') }}" class="block group bg-surface-container-lowest border-[1.5px] border-outline-variant rounded-md p-6 cursor-pointer hover:border-primary hover:shadow-card hover:-translate-y-0.5 transition-all">
                <div class="w-11 h-11 rounded-[10px] bg-primary-fixed text-primary flex items-center justify-center text-lg mb-3.5 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                    <i class="fa-solid {{ $cat->icon ?? 'fa-calendar-days' }}"></i>
                </div>
                <div class="font-bold text-primary text-[15px]">{{ $cat->nama_kategori }}</div>
            </a>
            @empty
            <div class="col-span-4 p-6 text-center text-outline bg-surface-container-lowest border border-outline-variant rounded-md">
                Belum ada data kategori di database.
            </div>
            @endforelse
        </div>
    </div>
</section>

<section id="events" class="pb-16">
    <div class="max-w-container mx-auto px-6 w-full">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between flex-wrap gap-3 mb-7">
            <div>
                <h2 class="text-headline-md font-extrabold text-primary">Event Terbaru</h2>
                <p class="text-[13px] text-on-surface-variant mt-1">Kegiatan kampus yang telah diverifikasi dan siap diikuti.</p>
            </div>
            <a href="{{ route('login') }}" class="group text-[13px] font-semibold text-primary flex items-center gap-1.5 transition-all hover:gap-2">Lihat Semua Event <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
            <article class="group bg-surface-container-lowest border-[1.5px] border-outline-variant rounded-md overflow-hidden hover:shadow-card hover:-translate-y-1 transition-all">
                <div class="relative h-[200px] overflow-hidden">
                    <img src="{{ $event->flyer_url ?? 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80' }}" alt="{{ $event->nama_event }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h3 class="text-title-lg text-primary mb-2.5 leading-snug line-clamp-2">{{ $event->nama_event }}</h3>
                    <div class="flex items-center gap-2 text-[12.5px] text-on-surface-variant mb-1.5">
                        <i class="fa-regular fa-calendar text-primary-container w-3.5"></i>
                        {{ $event->timeLines->where('nama_timeline', 'Pelaksanaan')->first()?->tanggal_mulai?->format('d M Y') ?? 'Segera Hadir' }}
                    </div>
                    <div class="flex items-center gap-2 text-[12.5px] text-on-surface-variant mb-1.5">
                        <i class="fa-solid fa-users text-primary-container w-3.5"></i>
                        {{ $event->penyelenggara ?? ($event->organisasi->nama_organisasi ?? 'Penyelenggara') }}
                    </div>
                    <a href="{{ route('login') }}" class="block w-full text-center bg-primary-fixed text-on-primary-fixed rounded px-3 py-2.5 font-semibold text-[13.5px] mt-3.5 hover:bg-primary hover:text-on-primary transition-colors">Lihat Detail</a>
                </div>
            </article>
            @empty
            <div class="col-span-3 p-10 text-center text-outline bg-surface-container-lowest border border-outline-variant rounded-md">
                Belum ada event yang dipublikasikan saat ini.
            </div>
            @endforelse
        </div>
    </div>
</section>

<div class="max-w-container mx-auto px-6 w-full mb-16">
    <div class="bg-primary rounded-xl px-6 py-12 md:py-16 text-center shadow-lg">
        <h2 class="text-headline-lg-mobile md:text-[36px] font-black text-on-primary mb-3.5 tracking-tight">Siap Memulai Event Pertamamu?</h2>
        <p class="text-inverse-primary max-w-[420px] mx-auto mb-8 text-[15px]">Bergabunglah dengan ribuan mahasiswa lainnya dan jadikan kegiatan kampusmu lebih terorganisir dan berkesan.</p>
        <div class="flex flex-wrap justify-center gap-3.5">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-7 py-3 rounded text-primary bg-surface-container-lowest hover:bg-surface-container transition-colors font-semibold text-[15px]">Daftar Sekarang</a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-7 py-3 rounded text-on-primary border-2 border-on-primary hover:bg-on-primary hover:text-primary transition-colors font-semibold text-[15px]">Pelajari Lebih Lanjut</a>
        </div>
    </div>
</div>

<footer class="border-t border-outline-variant pt-14 pb-8 bg-surface-container-lowest">
    <div class="max-w-container mx-auto px-6 w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.8fr_1fr_1fr_1fr] gap-10 mb-12">
            <div>
                <div class="text-xl font-extrabold text-primary mb-3">Eventoria</div>
                <p class="text-[13.5px] text-on-surface-variant leading-relaxed max-w-[220px]">
                    Solusi manajemen event kampus paling terpercaya untuk efisiensi akademik.
                </p>
            </div>

            <?php foreach ($footer_links as $title => $links): ?>
            <div>
                <div class="text-[11px] font-bold tracking-[1.2px] uppercase text-on-surface mb-4"><?= htmlspecialchars($title) ?></div>
                <ul class="flex flex-col gap-2.5 list-none">
                    <?php foreach ($links as $link): ?>
                    <li><a href="{{ route('login') }}" class="text-[13.5px] text-outline hover:text-primary transition-colors"><?= htmlspecialchars($link) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="border-t border-outline-variant pt-6 text-center text-[12.5px] text-outline">
            &copy; <?= date('Y') ?> Eventoria Academic Management. All rights reserved.
        </div>
    </div>
</footer>

<script>
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
        });

        hamburger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('flex');
            }
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
            });
        });
    }
</script>

</body>
</html>