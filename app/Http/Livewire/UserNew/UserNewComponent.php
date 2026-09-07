<?php

namespace App\Http\Livewire\UserNew;

use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserNewComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        $users = User::where('username', 'like', '%' . $this->search . '%')
            ->orwhere('email', 'like', '%' . $this->search . '%')
            ->orwhere('ruc', 'like', '%' . $this->search . '%')
            ->orwhere('firstname', 'like', '%' . $this->search . '%')
            ->orwhere('lastname', 'like', '%' . $this->search . '%')
            ->paginate(20);
        return view('livewire.user-new.user-new-component', compact('users'));
    }
}
