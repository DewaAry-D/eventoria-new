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
        return Event::whereHas('organisasi', function ($q) {
            $this->fakultasId
                ? $q->where('fakultas_id', $this->fakultasId)
                : $q->where('tingkat_organisasi', 'universitas');
        });
    }

    private function calculateStatCards()
    {
        $counts = $this->baseEventQuery()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = array_change_key_case($counts, CASE_LOWER);

        $total = array_sum($counts);
        $pending = $counts[strtolower(EventStatus::PENDING_APPROVAL->value)] ?? 0;
        $approved = $counts[strtolower(EventStatus::PUBLISHED->value)] ?? 0;
        $rejected = $counts[strtolower(EventStatus::REVISION->value)] ?? 0;

        return [
            'total'    => $total,
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
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