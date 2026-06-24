<?php

namespace App\Livewire\Admin; // Sesuaikan dengan namespace Anda

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use App\Enums\EventStatus;
use Illuminate\Support\Facades\Auth;

class EventDetail extends Component
{
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('livewire.admin.event-detail');
    }
}