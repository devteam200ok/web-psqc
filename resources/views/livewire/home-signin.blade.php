<div wire:ignore.self class="modal modal-blur" id="signinModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($page == 'Signin')
                        로그인
                    @elseif ($page == 'Signup')
                        가입
                    @elseif ($page == 'Forgot Password')
                        비밀번호 찾기
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="닫기"></button>
            </div>
            <div class="modal-body">
                <form
                    wire:submit.prevent="{{ $page == 'Signin'
                        ? 'signin'
                        : ($page == 'Signup'
                            ? 'signup'
                            : ($page == 'Forgot Password' && $codeMatch
                                ? 'resetPassword'
                                : 'sendResetCode')) }}">

                    <div class="mb-3">
                        <label class="form-label">이메일</label>
                        <input wire:model.defer="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($page == 'Signup')
                        <div class="mb-3">
                            <label class="form-label">이름</label>
                            <input wire:model.defer="name" type="text"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Enter your name" />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if ($page == 'Forgot Password' && $resetField != false)
                        <div class="mb-3">
                            <label class="form-label">인증 코드</label>
                            <div class="input-group">
                                <input wire:model.defer="resetCode" type="text"
                                    class="form-control @error('resetCode') is-invalid @enderror"
                                    placeholder="인증 코드" />
                                <button wire:loading.attr="disabled" wire:click="verifyResetCode" type="button"
                                    class="btn btn-dark">
                                    인증 코드 확인
                                </button>
                            </div>
                            @error('resetCode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if ($page != 'Forgot Password' || $codeMatch == true)
                        <div class="mb-2">
                            <label class="form-label">
                                @if ($page == 'Forgot Password')
                                    새로운 비밀번호
                                @else
                                    비밀번호
                                @endif
                            </label>
                            <div class="input-group input-group-flat">
                                <input wire:model.defer="password" type="{{ $passwordType }}"
                                    class="form-control password @error('password') is-invalid @enderror"
                                    placeholder="Please enter your password" autocomplete="off" />
                                <span class="input-group-text ps-2">
                                    <span wire:click="togglePasswordType" class="link-secondary" style="cursor: pointer;">
                                        @if ($passwordType == 'password')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-eye-off">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
                                                <path
                                                    d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" />
                                                <path d="M3 3l18 18" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        @endif
                                    </span>
                                </span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="form-footer">
                        @include('inc.component.message')
                        @if ($page == 'Signin')
                            <button type="submit" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">로그인</button>
                        @elseif ($page == 'Signup')
                            <button type="submit" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">가입</button>
                        @elseif ($page == 'Forgot Password' && $resetCode == '')
                            <button type="submit" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">인증 코드 전송
                            </button>
                        @elseif ($page == 'Forgot Password' && $codeMatch == true)
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">비밀번호 재설정</button>
                        @endif
                    </div>
                </form>
                <div class="hr-text">or</div>

                <div class="row mb-3">
                    <div class="col">
                        <a href="{{ route('github.login') }}" class="btn btn-4 w-100">
                            <i class="ti ti-brand-github me-2" style="font-size:20px"></i>
                            GitHub로 로그인
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('google.login') }}" class="btn btn-4 w-100">
                            <i class="ti ti-brand-google me-2" style="font-size:20px"></i>
                            Google로 로그인
                        </a>
                    </div>
                </div>

                @if (env('APP_ENV') == 'local' || config('app.env') == 'local')
                    <hr class="mb-2">
                    <div class="d-flex">
                        <button type="button" class="btn btn-dark ms-auto" wire:click="quickLogin('client')">
                            클라이언트로 로그인
                        </button>
                        <button type="button" class="btn btn-dark ms-2" wire:click="quickLogin('admin')">
                            관리자 로그인
                        </button>
                    </div>
                    <hr class="mt-2 mb-4">
                @endif

                @if ($page == 'Signin')
                    <div class="text-center text-secondary mt-2">
                        비밀번호를 잊으셨나요?
                        <span wire:click="setPage('Forgot Password')" class="text-primary" style="cursor: pointer;" tabindex="-1">비밀번호 재설정</span>
                    </div>
                @elseif($page == 'Forgot Password')
                    <div class="text-center text-secondary mt-2">
                        비밀번호를 기억하시나요?
                        <span wire:click="setPage('Signin')" class="text-primary" style="cursor: pointer;" tabindex="-1">로그인</span>
                    </div>
                @endif

                @if ($page == 'Signup')
                    <div class="text-center text-secondary mt-2">
                        이미 계정이 있으신가요?
                        <span wire:click="setPage('Signin')" class="text-primary" style="cursor: pointer;" tabindex="-1">로그인</span>
                    </div>
                @else
                    <div class="text-center text-secondary mt-2">
                        계정이 없으신가요?
                        <span wire:click="setPage('Signup')" class="text-primary" style="cursor: pointer;" tabindex="-1">가입</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
