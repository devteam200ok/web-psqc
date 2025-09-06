@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-xl-8 mb-2">
                <button wire:loading.attr="disabled" wire:click="backupDatabase" class="btn btn-primary mb-3">
                    백업 생성
                </button>
                <ul class="list-group">
                    @forelse ($backups as $backup)
                        <li class="list-group-item d-flex align-items-center">
                            <div>
                                {{ $backup['name'] }} <br>
                                <small class="text-muted">💾 {{ $backup['size'] }}</small>
                            </div>
                            <button wire:confirm="백업 파일을 삭제하시겠습니까?" wire:click="deleteBackup({{ json_encode($backup['name']) }})" wire:loading.attr="disabled"
                                class="btn btn-sm btn-danger px-3 ms-auto me-2">
                                삭제
                            </button>
                            <a href="{{ route('admin.backup.download', ['filename' => $backup['name']]) }}"
                                class="btn btn-sm btn-dark" download>
                                다운로드
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">백업 파일이 없습니다.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
