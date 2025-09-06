<div class="nav-item dropdown">
    <a href="javascript:void(0)" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">

        @php
            $profileImage = auth()->user()->profile_image != null ? '/storage/user/profile_image/100/' . auth()->user()->profile_image : '/theme/no_profile_image.webp';
        @endphp

        <span class="avatar avatar-sm" style="background-image: url({{ $profileImage }})">
        </span>
        <div class="d-none d-xl-block ps-2">
            <div>{{auth()->user()->name}}</div>
            <div class="mt-1 small text-secondary">{{auth()->user()->email}}</div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <a href="{{ url('/') }}/{{ auth()->user()->role }}/account/profile" class="dropdown-item">프로필 수정</a>
        <a href="{{ url('/') }}/{{ auth()->user()->role }}/account/password" class="dropdown-item">비밀번호 변경</a>
        <div class="dropdown-divider"></div>
        <a href="javascript:void(0)" wire:click="logout" class="dropdown-item">로그아웃</a>
    </div>
</div>
