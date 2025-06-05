<?php

namespace App\Livewire\LandingPage;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.landing-page')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.landing-page.index');
    }
}
