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
                    <h2 class="page-title">프로필 수정</h2>
                    <div class="page-pretitle">계정 세부정보를 관리하고 프로필 이미지를 업데이트합니다.</div>
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
                            <h4 class="card-title mb-3">프로필 이미지</h4>

                            <form wire:submit.prevent="saveProfileImage" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="profile_image" class="form-label">업로드 이미지 (jpg, jpeg, png)</label>
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
                                        이미지 저장
                                    </button>
                                </div>
                            </form>

                            @if (Auth::user()->profile_image)
                                <hr class="my-3">
                                <h6>현재 프로필:</h6>
                                <img src="{{ asset('storage/user/profile_image/100/' . Auth::user()->profile_image) }}"
                                    alt="Profile" width="100" class="rounded">
                                <div class="mt-2">
                                    <button wire:click="deleteProfileImage" wire:loading.attr="disabled"
                                        class="btn btn-sm btn-danger">
                                        이미지 삭제
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3 text-danger">회원 탈퇴</h4>
                            <div class="alert alert-danger d-block">
                                <strong>주의!</strong><br>
                                계정을 삭제하면 프로필 이미지 등 계정에 연결된 데이터가 삭제되며, 이 작업은 되돌릴 수 없습니다.
                            </div>

                            <form wire:submit.prevent="deleteAccount">
                                {{-- 확인 문구 --}}
                                <div class="mb-3">
                                    <label class="form-label">확인 문구 입력 *</label>
                                    <input type="text" class="form-control" placeholder="탈퇴합니다 를 정확히 입력"
                                        wire:model.defer="deleteConfirmText">
                                    @error('deleteConfirmText')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- 비밀번호 확인 (일반 가입자만 노출) --}}
                                @if ($requiresPassword)
                                    <div class="mb-3">
                                        <label class="form-label">비밀번호 확인 *</label>
                                        <input type="password" class="form-control" wire:model.defer="deletePassword"
                                            autocomplete="current-password">
                                        @error('deletePassword')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endif

                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger" wire:loading.attr="disabled"
                                        onclick="return confirm('정말로 계정을 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')">
                                        영구 탈퇴하기
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
