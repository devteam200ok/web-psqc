@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        <div class="row">
            <div class="col-xl-6 mb-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">프로필 수정</h4>
                        <hr class="my-2">

                        <form wire:submit.prevent="saveProfileImage" enctype="multipart/form-data">
                            <div class="mb-2">
                                <label for="profile_image" class="form-label">이미지 업로드 (jpg, jpeg, png)</label>
                                <input wire:model="profile_image" type="file" id="profile_image" class="form-control"
                                    accept="image/jpg, image/jpeg, image/png">
                                @error('profile_image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            @if ($profile_image)
                                <div class="mb-2">
                                    <img src="{{ $profile_image->temporaryUrl() }}" alt="Preview" width="100">
                                </div>
                            @endif

                            <div class="text-end">
                                <button class="btn btn-primary" type="submit" wire:loading.attr="disabled">
                                    이미지 저장
                                </button>
                            </div>
                        </form>

                        @if (Auth::user()->profile_image)
                            <hr class="my-3">
                            <h5>현재 이미지:</h5>
                            <img src="{{ asset('storage/user/profile_image/100/' . Auth::user()->profile_image) }}"
                                alt="Profile" width="100">
                            <div class="mt-2">
                                <button wire:click="deleteProfileImage" wire:loading.attr="disabled"
                                    class="btn btn-sm btn-danger">
                                    이미지 삭제
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
