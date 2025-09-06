@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <input type="text" name="fake_name" autocomplete="name" style="display:none">
        <input type="email" name="fake_email" autocomplete="email" style="display:none">
        <input type="password" name="fake_password" autocomplete="new-password" style="display:none">
        <div>
            <div class="row">
                <div class="col-auto mb-2">
                    <input wire:model.live="search" wire:debounce.500ms class="form-control" type="search"
                        placeholder="Search.." aria-label="Search" />
                    <span class="fas fa-search search-box-icon"></span>
                </div>
                <div class="col-auto d-flex align-items-center mb-2">
                    <a wire:click="changeRole('all')" class="px-2 {{ $role == 'all' ? 'text-primary' : 'text-dark' }}"
                        href="javascript:void(0)">
                        <span>모든 역할({{ App\Models\User::count() }})</span>
                    </a>
                    <a wire:click="changeRole('client')"
                        class="px-2 {{ $role == 'client' ? 'text-primary' : 'text-dark' }}" href="javascript:void(0)">
                        <span>사용자({{ App\Models\User::where('role', 'client')->count() }})</span>
                    </a>
                    <a wire:click="changeRole('admin')"
                        class="px-2 {{ $role == 'admin' ? 'text-primary' : 'text-dark' }}" href="javascript:void(0)">
                        <span>관리자({{ App\Models\User::where('role', 'admin')->count() }})</span>
                    </a>
                </div>
                <div class="col-auto mb-2 ms-auto">
                    <button wire:click="openCreate" class="btn btn-primary">
                        @if ($userCreate == 'close')
                            <span class="ti ti-plus me-2"></span> 새로운 사용자
                        @else
                            <span class="ti ti-chevron-up me-2"></span> 닫기
                        @endif
                    </button>
                </div>
            </div>
            @if ($userCreate == 'open')
                <div class="card mb-2">
                    <div class="card-body">
                        <h3>새로운 사용자</h3>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">이메일</h5>
                                <div class="input-group">
                                    <input wire:model.live="createUserEmail" type="email" class="form-control">
                                    <button wire:click="createUserEmailCheck"
                                        class="btn btn-secondary px-2">중복 확인</button>
                                </div>
                                @if ($createUserEmailCheckResult == true)
                                    <small class="text-primary">* 사용 가능</small>
                                @endif
                            </div>
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">이름</h5>
                                <input wire:model="createUserName" type="text" class="form-control"
                                    placeholder="name">
                            </div>
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">비밀번호</h5>
                                <input wire:model="createUserPassword" type="password" class="form-control">
                            </div>
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">역할</h5>
                                <select wire:model="createUserRole" class="form-select">
                                    <option value="client">사용자</option>
                                    <option value="admin">관리자</option>
                                </select>
                            </div>
                            <div class="col-xl-12 mb-2 d-flex">
                                <button wire:click="newUserStore" wire:loading.attr="disabled"
                                    class="btn btn-primary ms-auto">저장</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if ($userEdit == 'open')
                <div class="card mb-2">
                    <div class="card-body">
                        <h3>사용자 수정</h3>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">이메일</h5>
                                <div class="input-group">
                                    <input wire:model="editUserEmail" type="email" class="form-control bg-light"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">이름</h5>
                                <input wire:model="editUserName" type="text" class="form-control" placeholder="name">
                            </div>
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">비밀번호</h5>
                                <input wire:model="editUserPassword" type="password" class="form-control">
                            </div>
                            <div class="col-xl-3 mb-2">
                                <h5 class="mb-1">역할</h5>
                                <select wire:model="editUserRole" class="form-select">
                                    <option value="client">사용자</option>
                                    <option value="admin">관리자</option>
                                </select>
                            </div>
                            <div class="col-xl-12 mt-1 mb-2 d-flex">
                                <button wire:click="closeEdit" class="btn btn-secondary ms-auto me-2">닫기</button>
                                <button wire:click="userUpdate" wire:loading.attr="disabled"
                                    class="btn btn-primary">수정</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <table class="table table-responsive table-sm table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>이메일</th>
                        <th>이름</th>
                        <th>역할</th>
                        <th>로그인</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="align-middle" wire:key="tr-{{ $user->id }}">
                            <td class="ps-1">
                                <small>{{ $user->id }}</small>
                            </td>
                            <td>
                                <span class="nav-link d-flex lh-1 p-0 px-2">
                                    @php
                                        $profileImage =
                                            $user->profile_image != null
                                                ? '/storage/user/profile_image/100/' . $user->profile_image
                                                : '/theme/no_profile_image.webp';
                                    @endphp

                                    <span class="avatar avatar-sm" style="background-image: url({{ $profileImage }})">
                                    </span>
                                    <div class="d-none d-xl-block ps-2">
                                        <div class="mt-1 small text-secondary">{{ $user->email }}</div>
                                    </div>
                                </span>
                            </td>
                            <td>
                                <small>{{ $user->name }}</small>
                            </td>
                            <td>
                                @if ($user->role == 'admin')
                                    <span class="badge bg-blue-lt text-blue-lt-fg">관리자</span>
                                @endif
                                @if ($user->role == 'client')
                                    <span class="badge bg-purple-lt text-purple-lt-fg">사용자</span>
                                @endif
                            </td>
                            <td>
                                <span wire:click="loginUser('{{ $user->id }}')" class="badge bg-dark text-white"
                                    style="cursor: pointer">로그인</span>
                            </td>
                            <td class="text-end pe-1">
                                <span wire:click="openEdit('{{ $user->id }}')" style="cursor: pointer"
                                    class="text-primary">
                                    <i class="ti ti-edit me-2"></i>
                                </span>
                                <span wire:confirm="delete?" wire:click="deleteUser('{{ $user->id }}')"
                                    class="text-danger" style="cursor: pointer">
                                    <i class="ti ti-trash"></i>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row mb-2">
                {{ $users->onEachSide(0)->links() }}
            </div>
        </div>

    </div>
</div>
@section('js')
@endsection
