<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventManagement extends Component
{
    public ?int $fakultasId = null;
    public array $statCards = [];

    public function mount()
    {
        // Ambil profil Admin DPM yang Login
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();
        $this->fakultasId = $adminDpm?->fakultas_id;

        $this->statCards = $this->calculateStatCards();
    }

    protected function baseEventQuery()
    {
        $query = Event::whereHas('organisasi', function ($q) {
            $this->fakultasId
                ? $q->where('fakultas_id', $this->fakultasId)
                : $q->where('tingkat_organisasi', 'universitas');
        });

        return $query->where('status', '!=', EventStatus::DRAFT->value);
    }

    private function calculateStatCards()
    {
        $counts = $this->baseEventQuery()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = array_change_key_case($counts, CASE_LOWER);

        $pending   = $counts[strtolower(EventStatus::PENDING_APPROVAL->value)] ?? 0;
        $revision  = $counts[strtolower(EventStatus::REVISION->value)] ?? 0;
        $published = $counts[strtolower(EventStatus::PUBLISHED->value)] ?? 0;
        $completed = $counts[strtolower(EventStatus::COMPLETED->value)] ?? 0;

        // jumlah dari seluruh event yang sudah resmi diajukan ke admin
        $total = $pending + $revision + $published + $completed;

        return [
            'total'     => $total,
            'pending'   => $pending,
            'published' => $published,
            'completed' => $completed,
            'revision'  => $revision,
        ];
    }

    #[On('trigger-global-refresh')]
    public function refreshComponent()
    {
        $this->statCards = $this->calculateStatCards();
    }

    // Memuat Layout Utama
    #[Layout('layouts.admin', ['active' => 'moderasi-event'])]
    public function render()
    {
        return view('livewire.pages.admin.event-management');
    }
}