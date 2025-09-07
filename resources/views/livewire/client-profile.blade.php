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
                    <h2 class="page-title">Edit Profile</h2>
                    <div class="page-pretitle">Manage your account details and update your profile image.</div>
                </div>
            </div>
        </div>
    </section>

    <div class="page-body">
        <div class="container-xl">
            @include('inc.component.message')

            <div class="row">
                <div class="col-xl-6 mb-3">
                    <div class="card mb-2">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Profile Image</h4>

                            <form wire:submit.prevent="saveProfileImage" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">Upload Image (jpg, jpeg, png)</label>
                                    <input wire:model="profile_image" type="file" id="profile_image"
                                        class="form-control" accept="image/jpg, image/jpeg, image/png">
                                    @error('profile_image')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                @if ($profile_image)
                                    <div class="mb-2">
                                        <img src="{{ $profile_image->temporaryUrl() }}" alt="Preview" width="100"
                                            class="rounded">
                                    </div>
                                @endif

                                <div class="text-end">
                                    <button class="btn btn-primary" type="submit" wire:loading.attr="disabled">
                                        Save Image
                                    </button>
                                </div>
                            </form>

                            @if (Auth::user()->profile_image)
                                <hr class="my-3">
                                <h6>Current Profile:</h6>
                                <img src="{{ asset('storage/user/profile_image/100/' . Auth::user()->profile_image) }}"
                                    alt="Profile" width="100" class="rounded">
                                <div class="mt-2">
                                    <button wire:click="deleteProfileImage" wire:loading.attr="disabled"
                                        class="btn btn-sm btn-danger">
                                        Delete Image
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3 text-danger">Delete Account</h4>
                            <div class="alert alert-danger d-block">
                                <strong>Warning!</strong><br>
                                Deleting your account will remove data linked to your account such as profile images, and this action cannot be undone.
                            </div>

                            <form wire:submit.prevent="deleteAccount">
                                {{-- Confirmation text --}}
                                <div class="mb-3">
                                    <label class="form-label">Enter confirmation text *</label>
                                    <input type="text" class="form-control" placeholder="Type exactly: DELETE ACCOUNT"
                                        wire:model.defer="deleteConfirmText">
                                    @error('deleteConfirmText')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Password confirmation (only for regular users) --}}
                                @if ($requiresPassword)
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" wire:model.defer="deletePassword"
                                            autocomplete="current-password">
                                        @error('deletePassword')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif

                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger" wire:loading.attr="disabled"
                                        onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                                        Delete Account Permanently
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@section('js')
@endsection
