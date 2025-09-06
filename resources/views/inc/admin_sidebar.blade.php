<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">

        @php
            $current_url = url()->current();
        @endphp

        <div class="navbar-brand navbar-brand-autodark d-flex d-lg-none">
            <a href="{{ url('/') }}/{{ auth()->user()->role }}/dashboard">
                @if (Storage::disk('public')->exists('branding/logo_white.svg'))
                    <img src="{{ asset('storage/branding/logo_white.svg') }}" style="max-width:120px">
                @endif
            </a>
        </div>

        <button class="navbar-toggler ms-auto me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a href="javascript:void(0)" class="nav-link d-flex d-lg-none lh-1 p-0 px-2" data-bs-toggle="dropdown"
            aria-label="Open user menu">

            @php
                $profileImage =
                    auth()->user()->profile_image != null
                        ? '/storage/user/profile_image/100/' . auth()->user()->profile_image
                        : '/theme/no_profile_image.webp';
            @endphp

            <span class="avatar avatar-sm" style="background-image: url({{ $profileImage }})">
            </span>
            <div class="d-none d-xl-block ps-2">
                <div>{{ auth()->user()->name }}</div>
                <div class="mt-1 small text-secondary">{{ auth()->user()->email }}</div>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <a href="{{ url('/') }}/{{ auth()->user()->role }}/account/profile" class="dropdown-item">프로필</a>
            <a href="{{ url('/') }}/{{ auth()->user()->role }}/account/password" class="dropdown-item">비밀번호</a>
            <div class="dropdown-divider"></div>
            <a href="{{url('/')}}/admin/logout" class="dropdown-item">로그아웃</a>
        </div>

        <div class="navbar-brand navbar-brand-autodark d-none d-lg-flex">
            <a href="{{ url('/') }}/{{ auth()->user()->role }}/dashboard" class="mt-2">
                @if (Storage::disk('public')->exists('branding/logo_white.svg'))
                    <img src="{{ asset('storage/branding/logo_white.svg') }}" style="max-width:120px">
                @endif
            </a>
        </div>

        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ str_contains($current_url, '/admin/dashboard') ? 'active' : '' }}">
                    <a class="nav-link {{ str_contains($current_url, '/admin/dashboard') ? 'active' : '' }}"
                        href="{{ url('/') }}/{{ auth()->user()->role }}/dashboard">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="nav-link-title"> 대시보드 </span>
                    </a>
                </li>

                @php
                    $menus = App\Models\Menu::where('menu_id', 0)->orderby('order', 'asc')->get();
                @endphp

                @foreach ($menus as $menu)
                    @if ($menu->role == auth()->user()->role)
                        @if ($menu->type === 'menu')
                            @php
                                $path = '/' . $menu->role . '/' . $menu->url;
                                $active = str_contains($current_url, $path) ? 'active' : '';
                            @endphp

                            <li class="nav-item {{ $active }}">
                                <a class="nav-link {{ $active }}"
                                    href="{{ url('/') }}{{ $path }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="{{ $menu->icon }}"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        {{ $menu->title }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        @if ($menu->type == 'group')
                            @php
                                $submenus = App\Models\Menu::where('menu_id', $menu->id)
                                    ->orderby('order', 'asc')
                                    ->get();
                                $isOpen = str_contains($current_url, '/' . $menu->role . '/' . $menu->url);
                            @endphp
                            <li class="nav-item {{ $isOpen ? 'active' : '' }} dropdown">
                                <a class="nav-link dropdown-toggle {{ $isOpen ? 'show' : '' }}"
                                    href="#navbar-{{ $menu->url }}" data-bs-toggle="dropdown"
                                    data-bs-auto-close="false" role="button"
                                    aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="{{ $menu->icon }}"></i>
                                    </span>
                                    <span class="nav-link-title">
                                        {{ $menu->title }}
                                    </span>
                                </a>
                                <div class="dropdown-menu {{ $isOpen ? 'show' : '' }}"
                                    data-bs-popper="{{ $isOpen ? 'static' : '' }}">
                                    @foreach ($submenus as $submenu)
                                        @php
                                            $path = '/' . $menu->role . '/' . $menu->url . '/' . $submenu->url;
                                            $active = str_contains($current_url, $path) ? 'active' : '';
                                        @endphp
                                        <a class="dropdown-item {{ $active }}"
                                            href="{{ url('/') }}{{ $path }}">
                                            {{ $submenu->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    @endif
                @endforeach

                @if (auth()->user()->role == 'admin')

                    <li class="nav-item {{ str_contains($current_url, '/admin/user') ? 'active' : '' }}">
                        <a class="nav-link {{ str_contains($current_url, '/admin/user') ? 'active' : '' }}"
                            href="{{ url('/') }}/admin/user">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-user"></i>
                            </span>
                            <span class="nav-link-title"> 사용자 </span>
                        </a>
                    </li>

                    @php
                        $settings = [
                            'branding' => '브랜딩',
                            'information' => '회사정보',
                            'seo' => 'SEO',
                            'api' => 'API 관리',
                            'privacy' => '개인정보 처리방침',
                            'terms' => '서비스 이용약관',
                        ];
                        $baseUrl = url('/admin/setting');
                        $isSettingOpen = str_contains($current_url, '/admin/setting');
                    @endphp
                    <li class="nav-item {{ $isSettingOpen ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle {{ $isSettingOpen ? 'show' : '' }}" href="#navbar-setting"
                            data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                            aria-expanded="{{ $isSettingOpen ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-settings"></i>
                            </span>
                            <span class="nav-link-title">설정</span>
                        </a>
                        <div class="dropdown-menu {{ $isSettingOpen ? 'show' : '' }}"
                            data-bs-popper="{{ $isSettingOpen ? 'static' : '' }}">
                            @foreach ($settings as $segment => $label)
                                @php
                                    $href = "{$baseUrl}/{$segment}";
                                    $active = str_contains($current_url, $href);
                                @endphp
                                <a class="dropdown-item {{ $active ? 'active' : '' }}" href="{{ $href }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    @php
                        $devBase = '/admin/development';
                        $devLinks = [
                            ['label' => '사용자 메뉴', 'uri' => "{$devBase}/navbar", 'env' => 'local'],
                            ['label' => '관리자 메뉴', 'uri' => "{$devBase}/menu", 'env' => 'local'],
                            ['label' => '데이터베이스', 'uri' => "{$devBase}/database", 'env' => 'local'],
                            ['label' => '로그', 'uri' => "{$devBase}/logs", 'target' => '_blank'],
                            ['label' => 'PhpInfo', 'uri' => "{$devBase}/php"],
                            ['label' => '백업', 'uri' => "{$devBase}/backup"],
                        ];
                        $isDevOpen = str_contains($current_url, $devBase);
                    @endphp
                    <li class="nav-item {{ $isDevOpen ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle {{ $isDevOpen ? 'show' : '' }}" href="#navbar-development"
                            data-bs-toggle="dropdown" data-bs-auto-close="false" role="button"
                            aria-expanded="{{ $isDevOpen ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="ti ti-code-circle-2"></i>
                            </span>
                            <span class="nav-link-title">시스템</span>
                        </a>
                        <div class="dropdown-menu {{ $isDevOpen ? 'show' : '' }}">
                            @foreach ($devLinks as $link)
                                @if (!isset($link['env']) || env('APP_ENV') === $link['env'])
                                    @php
                                        $isActive = str_contains($current_url, $link['uri']);
                                    @endphp
                                    <a class="dropdown-item {{ $isActive ? 'active' : '' }}"
                                        href="{{ url($link['uri']) }}"
                                        @isset($link['target']) target="{{ $link['target'] }}" @endisset>
                                        {{ $link['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </li>
                @endif

                <li class="nav-item mt-3">
                    <a class="nav-link" href="{{ url('/') }}" target="_blank">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <i class="ti ti-home"></i>
                        </span>
                        <span class="nav-link-title"> 랜딩 페이지 </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
