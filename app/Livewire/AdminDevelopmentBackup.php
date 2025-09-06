<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class AdminDevelopmentBackup extends Component
{
    public $backups = [];

    public function mount()
    {
        $this->loadBackups();
    }
    
    public function backupDatabase()
    {
        \Artisan::call('backup:run');
        $this->loadBackups();
        session()->flash('success', 'Backup has been created successfully.');
    }

    public function deleteBackup($filename)
    {
        $path = env('APP_NAME') . '/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            $this->loadBackups();
            session()->flash('success', 'Backup has been deleted successfully.');
        } else {
            session()->flash('error', 'Backup file not found.');
        }
    }

    private function loadBackups()
    {
        $path = env('APP_NAME');
        $files = Storage::disk('local')->files($path);

        $this->backups = collect($files)
            ->filter(fn($file) => str_ends_with($file, '.zip'))
            ->map(function ($file) {
                return [
                    'name' => basename($file),
                    'size' => number_format(Storage::disk('local')->size($file) / 1024 / 1024, 2) . ' MB'
                ];
            })->sortByDesc('created_at')->values()->toArray();
    }

    public function render()
    {
        return view('livewire.admin-development-backup')
            ->layout('layouts.admin');
    }
}