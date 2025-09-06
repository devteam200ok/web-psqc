@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        <div class="row">
            <div class="col-xl-6 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">비밀번호 변경</h4>
                        <hr class="my-2">

                        <div class="mb-2">
                            <label for="current_password" class="form-label">현재 비밀번호</label>
                            <input wire:model.defer="current_password" type="password" class="form-control" id="current_password" autocomplete="current-password">
                        </div>

                        <div class="mb-2">
                            <label for="new_password" class="form-label">새 비밀번호</label>
                            <input wire:model.defer="new_password" type="password" class="form-control" id="new_password" autocomplete="new-password">
                        </div>
                        <div class="mb-2">
                            <label for="new_password_confirmation" class="form-label">새 비밀번호 확인</label>
                            <input wire:model.defer="new_password_confirmation" type="password" class="form-control" id="new_password_confirmation" autocomplete="new-password">
                        </div>
                        @if($errors->has('new_password'))
                            <div class="text-danger mb-2">{{ $errors->first('new_password') }}</div>
                        @endif
                        <small class="text-muted mb-4 d-block">
                            비밀번호는 8–15자여야 하며 대문자, 소문자, 숫자, 특수 문자를 각각 3가지 이상 포함해야 합니다.
                        </small>
                        
                        <div class="text-end">
                            <button wire:click="updatePassword" wire:loading.attr="disabled" class="btn btn-primary">
                                비밀번호 변경
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection