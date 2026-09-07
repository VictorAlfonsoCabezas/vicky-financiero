<?php

namespace App\Http\Livewire\ProformaHeader;

use App\Models\ProformaHeader;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProformaHeaderComponent extends Component
{
    public function render()
    {
        $header = ProformaHeader::paginate(10);
        return view('livewire.proforma-header.proforma-header-component', compact('header'));
    }
}
