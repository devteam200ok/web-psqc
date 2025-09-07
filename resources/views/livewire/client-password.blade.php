@section('title')
    @include('inc.component.seo')
@endsection

@section('css')
@endsection

<div>
    <section class="pt-4">
        <div class="container-xl px-3">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Change Password</h2>
                    <div class="page-pretitle">Change your account password.</div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')

            <div class="row">
                <div class="col-xl-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Change Password</h4>
                            <hr class="my-2">

                            <div class="mb-2">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input wire:model.defer="current_password" type="password" class="form-control"
                                    id="current_password" autocomplete="current-password">
                            </div>

                            <div class="mb-2">
                                <label for="new_password" class="form-label">New Password</label>
                                <input wire:model.defer="new_password" type="password" class="form-control"
                                    id="new_password" autocomplete="new-password">
                            </div>
                            <div class="mb-2">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <input wire:model.defer="new_password_confirmation" type="password" class="form-control"
                                    id="new_password_confirmation" autocomplete="new-password">
                            </div>
                            @if ($errors->has('new_password'))
                                <div class="text-danger mb-2">{{ $errors->first('new_password') }}</div>
                            @endif
                            <small class="text-muted mb-4 d-block">
                                Password must be 8-15 characters and include at least 3 of the following: uppercase letters, lowercase letters, numbers, and special characters.
                            </small>

                            <div class="text-end">
                                <button wire:click="updatePassword" wire:loading.attr="disabled"
                                    class="btn btn-primary">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@section('js')
@endsection
