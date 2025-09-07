@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-xl-8 mb-2">
                <button wire:loading.attr="disabled" wire:click="backupDatabase" class="btn btn-primary mb-3">
                    Create Backup
                </button>
                <ul class="list-group">
                    @forelse ($backups as $backup)
                        <li class="list-group-item d-flex align-items-center">
                            <div>
                                {{ $backup['name'] }} <br>
                                <small class="text-muted">💾 {{ $backup['size'] }}</small>
                            </div>
                            <button wire:confirm="Are you sure you want to delete this backup file?" 
                                wire:click="deleteBackup({{ json_encode($backup['name']) }})" 
                                wire:loading.attr="disabled"
                                class="btn btn-sm btn-danger px-3 ms-auto me-2">
                                Delete
                            </button>
                            <a href="{{ route('admin.backup.download', ['filename' => $backup['name']]) }}"
                                class="btn btn-sm btn-dark" download>
                                Download
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No backup files found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection