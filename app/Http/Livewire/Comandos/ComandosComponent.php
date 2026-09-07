<?php

namespace App\Http\Livewire\Comandos;

use App\Models\Admin\Menu;
use Livewire\Component;

class ComandosComponent extends Component
{

    public function render()
    {
        $comandos = Menu::paginate(10);
        return view('livewire.comandos.comandos-component',compact('comandos'));
    }
}
