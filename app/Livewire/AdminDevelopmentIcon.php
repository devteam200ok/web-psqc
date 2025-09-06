<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AdminDevelopmentIcon extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $SearchIcon = '';

    public function selectIcon($text)
    {
        $this->dispatch('icon_selected', icon: $text);
    }

    public function updatedSearchIcon()
    {
        $this->resetPage();
    }

    public function render()
    {
        $icons = DB::table('icons')->where('title', 'like', '%'.$this->SearchIcon.'%')
            ->orderBy('id', 'asc')
            ->paginate(36);
        return view('livewire.admin-development-icon')
            ->with('icons', $icons);
    }
}
