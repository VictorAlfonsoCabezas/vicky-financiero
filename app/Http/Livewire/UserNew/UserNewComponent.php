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

    public $estado = '';
    public function updatingSearch() { $this->resetPage(); }
    public function updatingEstado() { $this->resetPage(); }

    public function usersQuery()
    {
        return User::where(function ($query) {
            $term = '%' . trim($this->search) . '%';
            $query->where('username', 'like', $term)->orWhere('email', 'like', $term)
                ->orWhere('ruc', 'like', $term)->orWhere('firstname', 'like', $term)->orWhere('lastname', 'like', $term);
        })->when(in_array($this->estado, ['0', '1'], true), function ($query) { $query->where('status', $this->estado); })
            ->orderBy('firstname')->orderBy('lastname')->orderBy('id');
    }

    public function render()
    {
        $users = $this->usersQuery()->paginate(18);
        return view('livewire.user-new.user-new-component', compact('users'));
    }
}
