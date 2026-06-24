<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

class EventManagement extends Component
{
    // Memuat Layout Utama
    #[Layout('layouts.admin', ['active' => 'moderasi-event'])]
    
    // Memuat Main Section
    public function render()
    {
        return view('livewire.pages.admin.event-management');
    }
}
