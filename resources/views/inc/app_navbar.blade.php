<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3 px-2">
            <a href="{{ url('/') }}" style="text-decoration: none; color: inherit;">
                <img src="{{ asset('storage/branding/logo_color.svg') }}" alt="Logo" width="120">
            </a>
        </div>

        <div class="navbar-nav flex-row order-md-last">
            @guest
                <div class="nav-item">
                    <span class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="modal" data-bs-target="#signinModal"
                        style="cursor: pointer;">
                        <svg class="d-none d-md-block" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon avatar-icon icon-2">
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        </svg>
                        <div class="ps-2 d-none d-md-block">
                            <span class="fw-bold">Sign In</span>
                        </div>
                        <span class="avatar avatar-2 bg-blue-lt d-block d-md-none">
                            <svg class="mt-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="icon avatar-icon icon-2">
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                            </svg>
                        </span>
                    </span>
                    @livewire('home-signin')
                </div>
            @else
                <div class="nav-item dropdown">
                    <span class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu"
                        style="cursor: pointer;">

                        <div class="d-none d-xl-block pe-2 text-end">
                            <div>{{ auth()->user()->name }}</div>
                            <div class="mt-1 small text-secondary">{{ auth()->user()->email }}</div>
                        </div>
                        <span class="avatar avatar-2 bg-blue-lt">
                            @if (auth()->user()->profile_image == null)
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon avatar-icon icon-2">
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                </svg>
                            @else
                                <img src="{{ url('/') }}/storage/user/profile_image/100/{{ auth()->user()->profile_image }}"
                                    alt="Profile Image" class="rounded">
                            @endif
                        </span>
                    </span>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ url('/') }}/client/certificate" class="dropdown-item">Certificate (Individual Web
                            Test)</a>
                        <a href="{{ url('/') }}/client/psqc" class="dropdown-item">Certificate (PSQC Comprehensive)</a>
                        <a href="{{ url('/') }}/client/plan" class="dropdown-item">Subscription & Payment</a>
                        <a href="{{ url('/') }}/client/profile" class="dropdown-item">Edit Profile</a>
                        <a href="{{ url('/') }}/client/password" class="dropdown-item">Change Password</a>
                        <div class="dropdown-divider"></div>
                        @if (auth()->user()->role == 'admin')
                            <a href="{{ url('/') }}/{{ auth()->user()->role }}/dashboard" class="dropdown-item">
                                Admin Dashboard
                            </a>
                        @endif
                        <a href="{{ url('/') }}/logout" class="dropdown-item">Sign Out</a>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</header>
<header class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar">
            <div class="container-xl">
                <div class="row flex-column flex-md-row flex-fill align-items-center">
                    <div class="col">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button">
                                    <span class="fw-bold text-dark">⚡ Performance</span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/performance/speed">
                                        <span class="fw-bold text-dark">Global Speed</span>
                                        <small class="text-muted d-block">Measure load speed across 8 regions</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/performance/load">
                                        <span class="fw-bold text-dark">Load Test</span>
                                        <small class="text-muted d-block">Simulate concurrent user traffic</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/performance/mobile">
                                        <span class="fw-bold text-dark">Mobile Test</span>
                                        <small class="text-muted d-block">Performance in mobile environments</small>
                                    </a>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button">
                                    <span class="fw-bold text-dark">🔒 Security</span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/security/ssl">
                                        <span class="fw-bold text-dark">SSL Basics</span>
                                        <small class="text-muted d-block">Certificate & config check</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/sslyze">
                                        <span class="fw-bold text-dark">SSL Advanced</span>
                                        <small class="text-muted d-block">TLS settings & cipher suites</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/header">
                                        <span class="fw-bold text-dark">Headers</span>
                                        <small class="text-muted d-block">HTTP security policies</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/scan">
                                        <span class="fw-bold text-dark">Vulnerability Scan</span>
                                        <small class="text-muted d-block">Dynamic app security test</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/nuclei">
                                        <span class="fw-bold text-dark">CVE Check</span>
                                        <small class="text-muted d-block">Latest known vulnerabilities</small>
                                    </a>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button">
                                    <span class="fw-bold text-dark">✨ Quality</span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/lighthouse">
                                        <span class="fw-bold text-dark">Lighthouse</span>
                                        <small class="text-muted d-block">SEO, performance, accessibility</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/accessibility">
                                        <span class="fw-bold text-dark">Accessibility</span>
                                        <small class="text-muted d-block">WCAG 2.1 checks</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/compatibility">
                                        <span class="fw-bold text-dark">Compatibility</span>
                                        <small class="text-muted d-block">Cross-browser testing</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/visual">
                                        <span class="fw-bold text-dark">Responsive UI</span>
                                        <small class="text-muted d-block">Viewport-based testing</small>
                                    </a>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button">
                                    <span class="fw-bold text-dark">📝 Content</span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/content/links">
                                        <span class="fw-bold text-dark">Links</span>
                                        <small class="text-muted d-block">Broken links & redirects</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/content/structure">
                                        <span class="fw-bold text-dark">Structured Data</span>
                                        <small class="text-muted d-block">Schema.org validation</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/content/crawl">
                                        <span class="fw-bold text-dark">Crawl</span>
                                        <small class="text-muted d-block">Batch site inspection</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/content/meta">
                                        <span class="fw-bold text-dark">Metadata</span>
                                        <small class="text-muted d-block">OG/Twitter card preview</small>
                                    </a>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}/pricing">
                                    <span class="fw-bold text-dark">🧾 Pricing</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}/certificate">
                                    <span class="fw-bold text-dark">📜 Certification</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col col-md-auto">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <div class="btn-list">
                                    <a class="btn btn-gradient mx-2 mt-3 mb-2 mt-md-2 w-100"
                                        href="{{ url('/') }}/request">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h6" />
                                                <path
                                                    d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                            </svg>
                                        </span>
                                        <span class="fw-bold text-white"> Contact Us </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
