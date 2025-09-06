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
                        <a href="{{ url('/') }}/client/certificate" class="dropdown-item">Certificate (Individual Web Test)</a>
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
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}">
                                    <span class="fw-bold text-dark">🏠 Home</span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="fw-bold text-dark">⚡ Performance (P) </span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/performance/speed">
                                        <span class="fw-bold text-dark">Global Speed Test</span>
                                        <small class="text-muted d-block">Loading speed measurement across 8 regional zones</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/performance/load">
                                        <span class="fw-bold text-dark">Load Testing</span>
                                        <small class="text-muted d-block">Performance measurement under concurrent user load</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/performance/mobile">
                                        <span class="fw-bold text-dark">Mobile Performance</span>
                                        <small class="text-muted d-block">Response and performance testing across mobile environments</small>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="fw-bold text-dark">🔒 Security (S) </span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/security/ssl">
                                        <span class="fw-bold text-dark">SSL Basics</span>
                                        <small class="text-muted d-block">SSL certificate and basic security configuration check</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/sslyze">
                                        <span class="fw-bold text-dark">SSL Advanced</span>
                                        <small class="text-muted d-block">In-depth analysis of TLS settings and cipher suites</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/header">
                                        <span class="fw-bold text-dark">Security Headers</span>
                                        <small class="text-muted d-block">HTTP security headers and policy validation</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/scan">
                                        <span class="fw-bold text-dark">Vulnerability Scan</span>
                                        <small class="text-muted d-block">Dynamic vulnerability detection for web applications</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/security/nuclei">
                                        <span class="fw-bold text-dark">Latest Vulnerabilities</span>
                                        <small class="text-muted d-block">CVE-based latest vulnerability pattern inspection</small>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="fw-bold text-dark">✨ Quality (Q) </span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/lighthouse">
                                        <span class="fw-bold text-dark">Overall Quality</span>
                                        <small class="text-muted d-block">Integrated analysis of performance, SEO, and accessibility</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/accessibility">
                                        <span class="fw-bold text-dark">Accessibility Deep Dive</span>
                                        <small class="text-muted d-block">Detailed web accessibility check based on WCAG 2.1</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/compatibility">
                                        <span class="fw-bold text-dark">Browser Compatibility</span>
                                        <small class="text-muted d-block">Cross-browser rendering tests</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/quality/visual">
                                        <span class="fw-bold text-dark">Responsive UI</span>
                                        <small class="text-muted d-block">Responsive UI testing across major viewports</small>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <span class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside" role="button" aria-expanded="false">
                                    <span class="fw-bold text-dark">📝 Content (C) </span>
                                </span>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ url('/') }}/content/links">
                                        <span class="fw-bold text-dark">Link Validation</span>
                                        <small class="text-muted d-block">Broken links and redirect chain inspection</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/content/structure">
                                        <span class="fw-bold text-dark">Structured Data</span>
                                        <small class="text-muted d-block">Schema.org markup validation</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/content/crawl">
                                        <span class="fw-bold text-dark">Site Crawling</span>
                                        <small class="text-muted d-block">Batch quality inspection of all pages</small>
                                    </a>
                                    <a class="dropdown-item" href="{{ url('/') }}/content/meta">
                                        <span class="fw-bold text-dark">Metadata</span>
                                        <small class="text-muted d-block">OG tags and Twitter card preview validation</small>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}/pricing">
                                    <span class="nav-link-title fw-bold text-dark">🧾 Pricing & Subscription</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}/certificate">
                                    <span class="nav-link-title fw-bold text-dark">📜 Certification & Scoring</span>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link" href="{{ url("/") }}/ranking">
                                    <span class="nav-link-title fw-bold text-dark">🏆 웹사이트 랭킹</span>
                                </a>
                            </li> --}}
                            {{-- Auto Generated Menu --}}
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
