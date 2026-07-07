<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.mahasiswa')] class extends Component
{
    public int $currentYear;
    public int $currentMonth;
    public string $selectedDate;
    public string $activeTab = 'semua'; // 'semua' | 'event-saya'
    public string $activeCategory = 'semua'; // 'semua' | category ID
    public string $calendarView = 'month'; // 'month' | 'week' | 'agenda'

    public function mount()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->selectedDate = now()->toDateString();
    }

    public function goToNextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    public function goToPrevMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    public function goToToday()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
        $this->selectedDate = now()->toDateString();
    }

    public function selectDate($dateStr)
    {
        $this->selectedDate = $dateStr;
    }

    public function changeView($view)
    {
        $this->calendarView = $view;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function setCategory($catId)
    {
        $this->activeCategory = $catId;
    }

    private function getExecTimeline($event)
    {
        return $event->timeLines->reject(function ($t) {
            return stripos($t->nama_timeline, 'pendaftaran') !== false
                || stripos($t->nama_timeline, 'registrasi') !== false
                || stripos($t->nama_timeline, 'registration') !== false;
        })->first() ?? $event->timeLines->first();
    }

    public function with(): array
    {
        $user = Auth::user()->load('mahasiswa.prodi');
        $mahasiswa = $user->mahasiswa;

        // Base query for published and personalized events
        $baseQuery = Event::with(['organisasi', 'kategori', 'timeLines'])
            ->where('status', 'published')
            ->where(function($q) use ($mahasiswa) {
                $q->where('tingkat_event', 'universitas')
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'fakultas')
                         ->whereHas('organisasi', fn($q3) => $q3->where('fakultas_id', $mahasiswa->prodi->fakultas_id));
                  })
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'prodi')
                         ->whereHas('organisasi', fn($q3) => $q3->where('prodi_id', $mahasiswa->prodi_id));
                  });
            });

        // Filter by tab: 'event-saya' vs 'semua'
        if ($this->activeTab === 'event-saya') {
            $baseQuery->whereHas('registrations', function($q) use ($mahasiswa) {
                $q->where('mahasiswa_id', $mahasiswa->id);
            });
        }

        // Filter by category if selected
        if ($this->activeCategory !== 'semua') {
            $baseQuery->where('kategori_id', $this->activeCategory);
        }

        $filteredEvents = $baseQuery->get();

        // 1. Calculate registered events this week count for header badge
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $registeredEventsThisWeekCount = EventRegistration::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('event', function($q) use ($startOfWeek, $endOfWeek) {
                $q->whereHas('timeLines', function($q2) use ($startOfWeek, $endOfWeek) {
                    $q2->where(function($sub) {
                        $sub->where('nama_timeline', 'not like', '%Pendaftaran%')
                            ->where('nama_timeline', 'not like', '%Registrasi%')
                            ->where('nama_timeline', 'not like', '%Registration%');
                    })
                    ->where(function($sub2) use ($startOfWeek, $endOfWeek) {
                        $sub2->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                             ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek])
                             ->orWhere(function($sub3) use ($startOfWeek, $endOfWeek) {
                                 $sub3->where('tanggal_mulai', '<=', $startOfWeek)
                                      ->where('tanggal_selesai', '>=', $endOfWeek);
                             });
                    });
                });
            })
            ->count();

        // 2. Generate Calendar grid days
        $days = [];
        $currentMonthStart = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfDay();
        $daysInMonth = $currentMonthStart->daysInMonth;
        
        // Find leading padding days from previous month
        $leadingCount = $currentMonthStart->dayOfWeekIso - 1; 
        for ($i = $leadingCount; $i > 0; $i--) {
            $day = $currentMonthStart->copy()->subDays($i);
            $days[] = $this->buildDayData($day, false, $filteredEvents);
        }

        // Days in active month
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $day = Carbon::create($this->currentYear, $this->currentMonth, $i)->startOfDay();
            $days[] = $this->buildDayData($day, true, $filteredEvents);
        }

        // Find trailing padding days to fill grid of full weeks
        $totalCellsSoFar = count($days);
        $totalCellsNeeded = ceil($totalCellsSoFar / 7) * 7;
        if ($totalCellsNeeded < 35) {
            $totalCellsNeeded = 35;
        }
        $trailingCount = $totalCellsNeeded - $totalCellsSoFar;
        for ($i = 1; $i <= $trailingCount; $i++) {
            $day = Carbon::create($this->currentYear, $this->currentMonth, $daysInMonth)->startOfDay()->addDays($i);
            $days[] = $this->buildDayData($day, false, $filteredEvents);
        }

        // 3. For the Week view: calculate the week of selectedDate (Monday to Sunday)
        $weekDays = [];
        $selectedCarbon = Carbon::parse($this->selectedDate);
        $weekStart = $selectedCarbon->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $weekDays[] = $this->buildDayData($day, $day->month === $this->currentMonth, $filteredEvents);
        }

        // 4. Agenda Hari Ini (events happening on selectedDate)
        $allPersonalizedEvents = Event::with(['organisasi', 'kategori', 'timeLines'])
            ->where('status', 'published')
            ->where(function($q) use ($mahasiswa) {
                $q->where('tingkat_event', 'universitas')
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'fakultas')
                         ->whereHas('organisasi', fn($q3) => $q3->where('fakultas_id', $mahasiswa->prodi->fakultas_id));
                  })
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'prodi')
                         ->whereHas('organisasi', fn($q3) => $q3->where('prodi_id', $mahasiswa->prodi_id));
                  });
            })
            ->get();

        $selectedDateStr = $this->selectedDate;
        $agendaEvents = $allPersonalizedEvents->filter(function($event) use ($selectedDateStr) {
            $execTimelines = $event->timeLines->reject(function ($t) {
                return stripos($t->nama_timeline, 'pendaftaran') !== false
                    || stripos($t->nama_timeline, 'registrasi') !== false
                    || stripos($t->nama_timeline, 'registration') !== false;
            });
            if ($execTimelines->isEmpty()) {
                $execTimelines = $event->timeLines;
            }
            if ($execTimelines->isEmpty()) {
                return $event->created_at->toDateString() === $selectedDateStr;
            }
            foreach ($execTimelines as $t) {
                if ($t->tanggal_mulai->toDateString() <= $selectedDateStr && $t->tanggal_selesai->toDateString() >= $selectedDateStr) {
                    return true;
                }
            }
            return false;
        });

        // Load registration statuses for the agenda events
        $registeredIds = EventRegistration::where('mahasiswa_id', $mahasiswa->id)
            ->whereIn('event_id', $agendaEvents->pluck('id'))
            ->get()
            ->keyBy('event_id');

        foreach ($agendaEvents as $e) {
            $e->registration_info = $registeredIds->get($e->id);
        }

        // 5. Upcoming Events (the next 3 published events starting from today)
        $upcomingEvents = Event::with(['timeLines', 'kategori'])
            ->where('status', 'published')
            ->where(function($q) use ($mahasiswa) {
                $q->where('tingkat_event', 'universitas')
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'fakultas')
                         ->whereHas('organisasi', fn($q3) => $q3->where('fakultas_id', $mahasiswa->prodi->fakultas_id));
                  })
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'prodi')
                         ->whereHas('organisasi', fn($q3) => $q3->where('prodi_id', $mahasiswa->prodi_id));
                  });
            })
            ->whereHas('timeLines', function($q) {
                $q->where(function($sub) {
                    $sub->where('nama_timeline', 'not like', '%Pendaftaran%')
                        ->where('nama_timeline', 'not like', '%Registrasi%')
                        ->where('nama_timeline', 'not like', '%Registration%');
                })
                ->where('tanggal_mulai', '>=', now()->startOfDay());
            })
            ->get()
            ->sortBy(function($event) {
                $exec = $this->getExecTimeline($event);
                return $exec ? $exec->tanggal_mulai->timestamp : 0;
            })
            ->take(3);

        foreach ($upcomingEvents as $e) {
            $exec = $this->getExecTimeline($e);
            $e->exec_date = $exec ? $exec->tanggal_mulai : null;
        }

        return [
            'days' => $days,
            'weekDays' => $weekDays,
            'agendaEvents' => $agendaEvents,
            'upcomingEvents' => $upcomingEvents,
            'registeredEventsThisWeekCount' => $registeredEventsThisWeekCount,
            'categories' => Kategori::all(),
            'currentMonthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->translatedFormat('F Y'),
            'mahasiswa' => $mahasiswa,
        ];
    }

    private function buildDayData(Carbon $day, bool $isCurrentMonth, $events): array
    {
        $dateStr = $day->toDateString();
        
        $dayEvents = $events->filter(function($event) use ($dateStr) {
            $execTimelines = $event->timeLines->reject(function ($t) {
                return stripos($t->nama_timeline, 'pendaftaran') !== false
                    || stripos($t->nama_timeline, 'registrasi') !== false
                    || stripos($t->nama_timeline, 'registration') !== false;
            });
            if ($execTimelines->isEmpty()) {
                $execTimelines = $event->timeLines;
            }
            if ($execTimelines->isEmpty()) {
                return $event->created_at->toDateString() === $dateStr;
            }
            foreach ($execTimelines as $t) {
                if ($t->tanggal_mulai->toDateString() <= $dateStr && $t->tanggal_selesai->toDateString() >= $dateStr) {
                    return true;
                }
            }
            return false;
        });

        return [
            'date' => $day,
            'dateStr' => $dateStr,
            'dayNum' => $day->day,
            'isCurrentMonth' => $isCurrentMonth,
            'isToday' => $day->isToday(),
            'isSelected' => $dateStr === $this->selectedDate,
            'events' => $dayEvents,
        ];
    }
}
?>

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-primary tracking-tight">Schedule Event</h1>
            <p class="text-sm text-on-surface-variant mt-1">Kelola jadwal event kampus Anda agar tidak terbentur dengan jadwal kuliah.</p>
        </div>
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary-fixed text-primary font-semibold text-xs rounded-full border border-outline-variant shadow-sm">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ $registeredEventsThisWeekCount }} event terdaftar minggu ini</span>
            </div>
        </div>
    </div>

    {{-- Main Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left Column: Interactive Calendar --}}
        <div class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm">
            
            {{-- Calendar Navigation & Toggles --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                {{-- Date / Navigation --}}
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-bold text-on-surface capitalize min-w-[140px]">{{ $currentMonthName }}</h2>
                    <div class="flex items-center border border-outline-variant rounded-lg overflow-hidden bg-surface-container-low">
                        <button wire:click="goToPrevMonth" class="p-1.5 hover:bg-surface-container-high text-on-surface-variant hover:text-primary transition duration-150" title="Bulan Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button wire:click="goToNextMonth" class="p-1.5 hover:bg-surface-container-high text-on-surface-variant hover:text-primary border-l border-outline-variant transition duration-150" title="Bulan Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    <button wire:click="goToToday" class="px-3 py-1.5 bg-surface-container-low border border-outline-variant rounded-lg text-xs font-semibold text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition duration-150">
                        Hari Ini
                    </button>
                </div>

                {{-- View Mode Toggle Group --}}
                <div class="inline-flex rounded-lg border border-outline-variant p-0.5 bg-surface-container-low">
                    <button wire:click="changeView('month')" class="px-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ $calendarView === 'month' ? 'bg-surface-container-lowest text-primary shadow-sm font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                        Month
                    </button>
                    <button wire:click="changeView('week')" class="px-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ $calendarView === 'week' ? 'bg-surface-container-lowest text-primary shadow-sm font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                        Week
                    </button>
                    <button wire:click="changeView('agenda')" class="px-3 py-1.5 rounded-md text-xs font-semibold transition duration-150 {{ $calendarView === 'agenda' ? 'bg-surface-container-lowest text-primary shadow-sm font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                        Agenda
                    </button>
                </div>
            </div>

            {{-- Tabs: Semua Event vs Event Saya --}}
            <div class="border-b border-outline-variant mb-4 flex gap-6">
                <button wire:click="setTab('semua')" class="pb-3 text-sm font-semibold transition relative {{ $activeTab === 'semua' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                    Semua Event
                    @if($activeTab === 'semua')
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full"></span>
                    @endif
                </button>
                <button wire:click="setTab('event-saya')" class="pb-3 text-sm font-semibold transition relative {{ $activeTab === 'event-saya' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                    Event Saya
                    @if($activeTab === 'event-saya')
                        <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full"></span>
                    @endif
                </button>
            </div>

            {{-- Category Pills --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-4 scrollbar-thin scrollbar-thumb-outline-variant">
                <button wire:click="setCategory('semua')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition duration-150 border {{ $activeCategory === 'semua' ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container-lowest text-on-surface border-outline-variant hover:bg-surface-container-low' }}">
                    Semua
                </button>
                @foreach($categories as $cat)
                    <button wire:click="setCategory('{{ $cat->id }}')" class="px-4 py-1.5 rounded-full text-xs font-semibold transition duration-150 border whitespace-nowrap {{ $activeCategory == $cat->id ? 'bg-primary text-on-primary border-primary' : 'bg-surface-container-lowest text-on-surface border-outline-variant hover:bg-surface-container-low' }}">
                        {{ str_replace('Workshop Teknologi', 'Workshop', str_replace('Seminar Nasional', 'Seminar', $cat->nama_kategori)) }}
                    </button>
                @endforeach
            </div>

            {{-- Calendar Rendering Engine --}}
            @if($calendarView === 'month')
                {{-- Month View Grid --}}
                <div class="grid grid-cols-7 border border-outline-variant rounded-xl overflow-hidden bg-surface-container-lowest shadow-sm">
                    {{-- Day Headers --}}
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Sen</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Sel</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Rab</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Kam</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Jum</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Sab</div>
                    <div class="bg-[#F1F3FA] border-b border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Min</div>
                    
                    {{-- Grid Cells --}}
                    @foreach($days as $index => $day)
                        @php
                            $isLastColumn = ($index + 1) % 7 === 0;
                            $isLastRow = $index >= count($days) - 7;
                        @endphp
                        <div wire:click="selectDate('{{ $day['dateStr'] }}')" class="min-h-[130px] p-2 {{ !$isLastColumn ? 'border-r' : '' }} {{ !$isLastRow ? 'border-b' : '' }} border-outline-variant flex flex-col justify-start items-stretch cursor-pointer transition {{ $day['isSelected'] ? 'bg-primary-fixed bg-opacity-15' : ($day['isCurrentMonth'] ? 'bg-surface-container-lowest hover:bg-surface-container-low' : 'bg-surface-container-low bg-opacity-35') }}">
                            {{-- Date Badge --}}
                            <div class="flex justify-between items-center mb-2 flex-shrink-0">
                                @if($day['isSelected'])
                                    <div class="flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold bg-primary text-white shadow-sm">
                                        {{ $day['dayNum'] }}
                                    </div>
                                @else
                                    <div class="pl-1.5 pt-0.5 text-xs font-semibold {{ $day['isCurrentMonth'] ? 'text-on-surface-variant' : 'text-outline-variant opacity-60' }}">
                                        {{ $day['dayNum'] }}
                                    </div>
                                @endif
                                @if($day['isToday'] && !$day['isSelected'])
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary mr-1"></span>
                                @endif
                            </div>
                            
                            {{-- Day's Event Badges --}}
                            <div class="space-y-1 overflow-hidden flex-1">
                                @if($day['isCurrentMonth'])
                                    @foreach($day['events']->take(2) as $ev)
                                        @php
                                            $isRegistered = $ev->registrations->where('mahasiswa_id', $mahasiswa->id)->isNotEmpty();
                                            $badgeClass = $isRegistered 
                                                ? 'bg-primary text-white font-semibold' 
                                                : 'bg-primary-fixed text-on-primary-fixed-variant border border-transparent';
                                        @endphp
                                        <div wire:click.stop="selectDate('{{ $day['dateStr'] }}')" class="block px-2 py-0.5 rounded text-[10px] truncate leading-normal transition hover:opacity-90 {{ $badgeClass }}" title="{{ $ev->nama_event }}">
                                            {{ $ev->nama_event }}
                                        </div>
                                    @endforeach
                                    @if($day['events']->count() > 2)
                                        <button wire:click.stop="selectDate('{{ $day['dateStr'] }}')" class="text-[9px] font-extrabold text-primary text-left pl-1.5 block hover:underline">
                                            +{{ $day['events']->count() - 2 }} more
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif($calendarView === 'week')
                {{-- Week View Grid --}}
                <div class="grid grid-cols-7 border border-outline-variant rounded-xl overflow-hidden bg-surface-container-lowest shadow-sm">
                    {{-- Day Headers --}}
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Sen</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Sel</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Rab</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Kam</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Jum</div>
                    <div class="bg-[#F1F3FA] border-b border-r border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Sab</div>
                    <div class="bg-[#F1F3FA] border-b border-outline-variant py-3.5 text-center text-xs font-bold text-on-surface-variant uppercase">Min</div>
                    
                    {{-- Week Row Cells --}}
                    @foreach($weekDays as $index => $day)
                        @php
                            $isLastColumn = ($index + 1) % 7 === 0;
                        @endphp
                        <div wire:click="selectDate('{{ $day['dateStr'] }}')" class="p-3 {{ !$isLastColumn ? 'border-r' : '' }} border-outline-variant flex flex-col justify-start items-stretch min-h-[14rem] cursor-pointer transition {{ $day['isSelected'] ? 'bg-primary-fixed bg-opacity-15' : 'bg-surface-container-lowest hover:bg-surface-container-low' }}">
                            <div class="flex justify-between items-center mb-2 flex-shrink-0">
                                @if($day['isSelected'])
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-primary text-white shadow-sm">
                                        {{ $day['dayNum'] }}
                                    </div>
                                @else
                                    <div class="pl-1.5 pt-0.5 text-sm font-semibold {{ $day['isCurrentMonth'] ? 'text-on-surface-variant' : 'text-outline-variant opacity-60' }}">
                                        {{ $day['dayNum'] }}
                                    </div>
                                @endif
                                @if($day['isToday'])
                                    <span class="px-2 py-0.5 rounded bg-primary text-white text-[9px] font-bold">HARI INI</span>
                                @endif
                            </div>
                            <div class="flex-1 space-y-1.5 overflow-y-auto">
                                @if($day['isCurrentMonth'])
                                    @forelse($day['events'] as $ev)
                                        @php
                                            $isRegistered = $ev->registrations->where('mahasiswa_id', $mahasiswa->id)->isNotEmpty();
                                            $badgeClass = $isRegistered 
                                                ? 'bg-primary text-white font-semibold' 
                                                : 'bg-primary-fixed text-on-primary-fixed-variant border border-transparent';
                                        @endphp
                                        <a href="{{ route('mahasiswa.event-detail', $ev->slug) }}" wire:navigate class="block px-2 py-1.5 rounded text-xs leading-tight transition hover:opacity-90 {{ $badgeClass }}">
                                            <div class="font-bold truncate">{{ $ev->nama_event }}</div>
                                            <div class="text-[9px] opacity-80 truncate mt-0.5">{{ $ev->kategori->nama_kategori }}</div>
                                        </a>
                                    @empty
                                        <div class="text-[10px] text-outline italic mt-1 pl-1">Tidak ada event</div>
                                    @endforelse
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif($calendarView === 'agenda')
                {{-- Agenda View --}}
                <div class="divide-y divide-outline-variant border-t border-b border-outline-variant mt-2">
                    @php
                        $activeMonthDaysWithEvents = collect($days)->filter(fn($d) => $d['isCurrentMonth'] && $d['events']->isNotEmpty());
                    @endphp
                    @forelse($activeMonthDaysWithEvents as $day)
                        <div class="py-4 flex flex-col md:flex-row md:items-start gap-4">
                            <div class="w-36 flex-shrink-0">
                                <button wire:click="selectDate('{{ $day['dateStr'] }}')" class="text-left group focus:outline-none">
                                    <div class="text-sm font-bold text-primary group-hover:underline capitalize">{{ $day['date']->translatedFormat('l, d M') }}</div>
                                    <div class="text-[11px] text-on-surface-variant mt-0.5 font-semibold">{{ $day['events']->count() }} Event</div>
                                </button>
                            </div>
                            <div class="flex-1 space-y-3">
                                @foreach($day['events'] as $ev)
                                    <div class="flex items-center justify-between p-3.5 bg-surface-container-low rounded-xl border border-outline-variant transition hover:bg-surface-container-high">
                                        <div class="pr-4">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-primary text-white uppercase mr-2">{{ str_replace('Workshop Teknologi', 'Workshop', str_replace('Seminar Nasional', 'Seminar', $ev->kategori->nama_kategori)) }}</span>
                                            <h4 class="inline text-sm font-bold text-on-surface leading-tight">{{ $ev->nama_event }}</h4>
                                            <p class="text-xs text-on-surface-variant mt-1.5 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>{{ $ev->nama_lokasi ?? 'Daring' }} • {{ $ev->penyelenggara }}</span>
                                            </p>
                                        </div>
                                        <a href="{{ route('mahasiswa.event-detail', $ev->slug) }}" wire:navigate class="flex-shrink-0 px-3.5 py-2 bg-primary text-on-primary text-xs font-bold rounded-lg hover:bg-primary-container transition-colors">
                                            Detail
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-on-surface-variant italic">
                            Tidak ada event terjadwal pada bulan {{ $currentMonthName }}.
                        </div>
                    @endforelse
                </div>
            @endif

        </div>

        {{-- Right Column: Agenda Hari Ini & Upcoming Events --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Card 1: Agenda Hari Ini --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-outline-variant pb-3 mb-4">
                    <h3 class="font-bold text-primary text-base">Agenda Hari Ini</h3>
                    <span class="text-xs font-semibold text-on-surface-variant capitalize">
                        {{ Carbon::parse($selectedDate)->translatedFormat('l, j M Y') }}
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($agendaEvents as $ev)
                        @php
                            $exec = $ev->timeLines->reject(function ($t) {
                                return stripos($t->nama_timeline, 'pendaftaran') !== false
                                    || stripos($t->nama_timeline, 'registrasi') !== false
                                    || stripos($t->nama_timeline, 'registration') !== false;
                            })->first() ?? $ev->timeLines->first();
                            $timeRange = $exec 
                                ? $exec->tanggal_mulai->format('H:i') . ' - ' . $exec->tanggal_selesai->format('H:i')
                                : 'Fleksibel';
                            $isReg = $ev->registration_info;
                        @endphp
                        
                        <div class="bg-surface border border-outline-variant rounded-xl p-4 space-y-3 transition hover:shadow-sm">
                            <div class="flex items-center justify-between gap-2">
                                @if($isReg)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-success bg-opacity-10 text-success uppercase">
                                        Terdaftar
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-surface-container-highest text-on-surface-variant uppercase">
                                        Belum Daftar
                                    </span>
                                @endif
                                <span class="text-xs text-on-surface-variant font-medium">{{ $timeRange }}</span>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-bold text-primary leading-tight line-clamp-2">{{ $ev->nama_event }}</h4>
                                <div class="text-xs text-on-surface-variant mt-2 flex items-start gap-1.5 leading-snug">
                                    <svg class="w-4 h-4 text-outline mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $ev->nama_lokasi ?? 'Daring' }}</span>
                                </div>
                            </div>

                            <div class="pt-1">
                                @if($isReg)
                                    <a href="{{ route('mahasiswa.my-events') }}" wire:navigate class="block text-center bg-primary text-on-primary w-full py-2 rounded-lg text-xs font-bold hover:bg-primary-container transition shadow-sm">
                                        Lihat Tiket
                                    </a>
                                @else
                                    <a href="{{ route('mahasiswa.event-detail', $ev->slug) }}" wire:navigate class="block text-center bg-surface-container-high text-primary border border-outline-variant w-full py-2 rounded-lg text-xs font-bold hover:bg-surface-container transition shadow-sm">
                                        Daftar Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-on-surface-variant italic text-xs">
                            Tidak ada agenda event pada tanggal ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Card 2: Upcoming Events --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center gap-1.5 border-b border-outline-variant pb-3 mb-4">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="font-bold text-primary text-base">Upcoming Events</h3>
                </div>

                <div class="space-y-4">
                    @forelse($upcomingEvents as $ev)
                        @php
                            $diff = now()->startOfDay()->diffInDays($ev->exec_date ? $ev->exec_date->startOfDay() : now()->startOfDay(), false);
                            if ($diff === 0) {
                                $rel = 'Hari ini';
                            } elseif ($diff === 1) {
                                $rel = 'Besok';
                            } elseif ($diff > 1) {
                                $rel = $diff . ' hari lagi';
                            } else {
                                $rel = abs($diff) . ' hari lalu';
                            }
                        @endphp
                        
                        <div class="flex items-start">
                            {{-- Date Square --}}
                            <div class="w-12 h-12 bg-surface-container border border-outline-variant rounded-xl flex flex-col items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-[9px] font-bold text-primary uppercase leading-none">
                                    {{ $ev->exec_date ? $ev->exec_date->translatedFormat('M') : 'TBA' }}
                                </span>
                                <span class="text-base font-extrabold text-on-surface leading-tight mt-0.5">
                                    {{ $ev->exec_date ? $ev->exec_date->format('d') : '--' }}
                                </span>
                            </div>
                            
                            {{-- Content Info --}}
                            <div class="flex-1 ml-3 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="bg-primary-fixed text-primary text-[8px] font-extrabold px-1.5 py-0.5 rounded uppercase leading-none">
                                        {{ $rel }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-primary mt-1 truncate hover:underline">
                                    <a href="{{ route('mahasiswa.event-detail', $ev->slug) }}" wire:navigate>
                                        {{ $ev->nama_event }}
                                    </a>
                                </h4>
                                <p class="text-[10px] text-on-surface-variant mt-0.5 truncate">{{ $ev->nama_lokasi ?? 'Daring' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-on-surface-variant italic text-xs">
                            Tidak ada event mendatang.
                        </div>
                    @endforelse
                </div>

                {{-- Footer Link --}}
                <div class="border-t border-outline-variant mt-4 pt-3 text-center">
                    <button wire:click="goToToday" class="text-xs font-bold text-primary hover:underline transition duration-150">
                        Lihat Semua Jadwal
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
