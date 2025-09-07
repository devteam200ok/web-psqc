<div wire:ignore.self class="modal modal-blur" id="signinModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if ($page == 'Signin')
                        Sign In
                    @elseif ($page == 'Signup')
                        Sign Up
                    @elseif ($page == 'Forgot Password')
                        Reset Password
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <label class="form-label">Email</label>
                        <input wire:model.defer="email" type="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($page == 'Signup')
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input wire:model.defer="name" type="text"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Enter your name" />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    @if ($page == 'Forgot Password' && $resetField != false)
                        <div class="mb-3">
                            <label class="form-label">Verification Code</label>
                            <div class="input-group">
                                <input wire:model.defer="resetCode" type="text"
                                    class="form-control @error('resetCode') is-invalid @enderror"
                                    placeholder="Enter verification code" />
                                <button wire:loading.attr="disabled" wire:click="verifyResetCode" type="button"
                                    class="btn btn-dark">
                                    Verify Code
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
                                    New Password
                                @else
                                    Password
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
                                class="btn btn-primary w-100">Sign In</button>
                        @elseif ($page == 'Signup')
                            <button type="submit" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">Sign Up</button>
                        @elseif ($page == 'Forgot Password' && $resetCode == '')
                            <button type="submit" wire:loading.attr="disabled"
                                class="btn btn-primary w-100">Send Verification Code
                            </button>
                        @elseif ($page == 'Forgot Password' && $codeMatch == true)
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary w-100">Reset Password</button>
                        @endif
                    </div>
                </form>
                <div class="hr-text">or</div>

                <div class="row mb-3">
                    <div class="col">
                        <a href="{{ route('github.login') }}" class="btn btn-4 w-100">
                            <i class="ti ti-brand-github me-2" style="font-size:20px"></i>
                            Sign in with GitHub
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('google.login') }}" class="btn btn-4 w-100">
                            <i class="ti ti-brand-google me-2" style="font-size:20px"></i>
                            Sign in with Google
                        </a>
                    </div>
                </div>

                @if (env('APP_ENV') == 'local' || config('app.env') == 'local')
                    <hr class="mb-2">
                    <div class="d-flex">
                        <button type="button" class="btn btn-dark ms-auto" wire:click="quickLogin('client')">
                            Login as Client
                        </button>
                        <button type="button" class="btn btn-dark ms-2" wire:click="quickLogin('admin')">
                            Login as Admin
                        </button>
                    </div>
                    <hr class="mt-2 mb-4">
                @endif

                @if ($page == 'Signin')
                    <div class="text-center text-secondary mt-2">
                        Forgot your password?
                        <span wire:click="setPage('Forgot Password')" class="text-primary" style="cursor: pointer;" tabindex="-1">Reset Password</span>
                    </div>
                @elseif($page == 'Forgot Password')
                    <div class="text-center text-secondary mt-2">
                        Remember your password?
                        <span wire:click="setPage('Signin')" class="text-primary" style="cursor: pointer;" tabindex="-1">Sign In</span>
                    </div>
                @endif

                @if ($page == 'Signup')
                    <div class="text-center text-secondary mt-2">
                        Already have an account?
                        <span wire:click="setPage('Signin')" class="text-primary" style="cursor: pointer;" tabindex="-1">Sign In</span>
                    </div>
                @else
                    <div class="text-center text-secondary mt-2">
                        Don't have an account?
                        <span wire:click="setPage('Signup')" class="text-primary" style="cursor: pointer;" tabindex="-1">Sign Up</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
