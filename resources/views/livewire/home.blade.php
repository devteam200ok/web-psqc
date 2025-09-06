@section('title')
    <title>Web-PSQC – Comprehensive Web Quality Testing (Performance · Security · Quality · Content)</title>
    <meta name="description"
        content="From global speed, load, and mobile performance to SSL, security headers, latest vulnerabilities, Lighthouse, accessibility, compatibility, links, structured data, crawling, and metadata. Diagnose web quality with 16 PSQC (Performance · Security · Quality · Content) tests and issue a certificate.">
    <meta name="keywords"
        content="web performance testing, web security scan, Lighthouse, accessibility, browser compatibility, structured data, site crawling, Web-PSQC, PSQC, certificate, global speed, k6 load testing, nuclei, ZAP">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Web-PSQC – Comprehensive Web Quality Testing" />
    <meta property="og:description" content="Diagnose your web across Performance, Security, Quality, and Content with 16 tests — and get an A+ grade certificate." />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="{{ App\Models\Setting::first()->og_image }}" />
    <meta property="og:image:alt" content="Web-PSQC Home Preview" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Web-PSQC – Comprehensive Web Quality Testing" />
    <meta name="twitter:description" content="Global performance, security, quality, and content in one place. 16 tests with certificate issuance." />
    <meta name="twitter:image" content="{{ App\Models\Setting::first()->og_image }}" />

    {{-- JSON-LD (WebSite + Organization + WebPage) --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Web-PSQC',
    'url'  => url('/'),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => url('/') . '?q={search_term_string}',
        'query-input' => 'required name=search_term_string'
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'DevTeam Co., Ltd.',
    'url'  => url('/'),
    'logo' => [
        '@type' => 'ImageObject',
        'url' => App\Models\Setting::first()->og_image ?? url('/images/og/default.png')
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Web-PSQC Home',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'description' =>
        'The Web-PSQC home page: diagnose web quality with 16 PSQC (Performance · Security · Quality · Content) standard tests.',
    'primaryImageOfPage' => [
        '@type' => 'ImageObject',
        'url' => App\Models\Setting::first()->og_image ?? url('/images/og/default.png')
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection
@section('css')
    <style>
        .hero-section {
            background: linear-gradient(135deg, #1a0a48, #030109);
            color: white;
            padding-top: 4rem;
            padding-bottom: 4rem;
            position: relative;
            overflow: hidden;
            min-height: 500px;
        }

        .hero-title {
            font-size: 2.75rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.125rem;
            line-height: 1.6;
        }

        .hero-cta-btn {
            font-size: 1.125rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            background: linear-gradient(90deg, #5f3dc4, #845ef7);
            color: #fff;
        }

        .hero-cta-btn:hover {
            color: #fff !important;
            background: linear-gradient(90deg, #5028bd, #6741d9);
        }

        .hero-cta-btn:active {
            color: #fff !important;
        }

        .hero-card {
            background-color: rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 0.5rem;
            padding: 1.2rem;
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.05);
            transition: transform 0.2s;
            height: 100%;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(4px);
            padding-bottom: 0px;
        }

        .hero-card:hover {
            transform: translateY(-4px);
        }

        .hero-card-title {
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .hero-card-desc {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .orbit-container {
            position: absolute;
            top: 50%;
            right: 20%;
            width: 240px;
            height: 240px;
            transform: translateY(-50%);
            z-index: 1;
            pointer-events: none;
        }

        .orbit {
            width: 100%;
            height: 100%;
            border: 1px dashed rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            position: relative;
            animation: spin 24s linear infinite;
        }

        .orbit-ball {
            width: 16px;
            height: 16px;
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, #7b68ee 40%, #3b0a75 90%);
            filter: blur(1.5px);
            box-shadow: 0 0 12px rgba(91, 48, 171, 0.4);
        }

        .orbit-ball:nth-child(1) {
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
        }

        .orbit-ball:nth-child(2) {
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .svg-icon-xl {
            width: 36px;
            height: 36px;
            display: block;
            margin: 0 auto 1rem auto;
        }

        .psqc-card {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            background: #fff;
        }

        .psqc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #845ef7;
        }

        .psqc-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .test-item {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .test-item:hover {
            border-color: #845ef7;
            background: #f8f9ff;
        }

        .badge-new {
            background: linear-gradient(45deg, #845ef7, #5f3dc4);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
        }

        .badge-new:hover {
            background: linear-gradient(45deg, #5f3dc4, #845ef7);
            color: white;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }

        .badge-coming {
            background: #f8f9fa;
            color: #6c757d;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            border: 1px solid #dee2e6;
        }

        .achievement-card {
            background: linear-gradient(135deg, #f8f9ff, #e7f1ff);
            border: 1px solid #845ef7;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
        }

        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #845ef7, #5f3dc4);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0 auto 1rem auto;
        }

        .pricing-highlight {
            background: linear-gradient(135deg, #e7f1ff, #f0f7ff);
            border: 2px solid #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #845ef7, #5f3dc4);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
    </style>
@endsection
<div>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container px-3">
            @include('inc.component.message')
            <div class="row align-items-center position-relative">
                <!-- Left text area -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <h1 class="hero-title d-none d-md-block mb-4">
                        Top 2% Web Quality Worldwide<br>For Your Website, Too
                    </h1>
                    <h2 class="h1 d-md-none mb-4">
                        Top 2% Web Quality Worldwide<br>For Your Website, Too
                    </h2>
                    <p class="hero-subtitle d-none d-md-block mb-4">
                        Google ranking, user experience, and security —<br>
                        diagnose everything in a single run.<br>
                        A comprehensive PSQC (Performance · Security · Quality · Content)<br>
                        web quality testing platform: Web-PSQC.
                    </p>
                    <p class="hero-subtitle d-md-none mb-4">
                        Google ranking, user experience, and security —
                        diagnose everything in a single run.
                        PSQC (Performance · Security · Quality · Content)
                        comprehensive web quality testing, by Web-PSQC.
                    </p>
                    <a href="#test" class="btn hero-cta-btn me-3">
                        Start Free Test
                    </a>
                    <a href="#pricing" class="btn btn-outline-light">
                        Pricing
                    </a>
                </div>

                <!-- Right feature cards -->
                <div class="col-md-5 offset-md-1 position-relative">
                    <!-- Orbit animation -->
                    <div class="orbit-container">
                        <div class="orbit">
                            <div class="orbit-ball"></div>
                            <div class="orbit-ball"></div>
                        </div>
                    </div>
                    <div class="row g-3 position-relative" style="z-index: 2;">
                        <div class="col-6">
                            <div class="hero-card mt-5">
                                <div class="hero-card-title">⚡ Performance Tests</div>
                                <div class="hero-card-desc">
                                    Global speed, load testing, and mobile performance — all in one
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-card">
                                <div class="hero-card-title">🔒 Security Checks</div>
                                <div class="hero-card-desc">
                                    Deep SSL analysis, security headers, and latest vulnerabilities across five checks
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-card mt-5">
                                <div class="hero-card-title">✨ Quality Analysis</div>
                                <div class="hero-card-desc">
                                    SEO, accessibility, browser compatibility, and UI regression validation
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-card">
                                <div class="hero-card-title">📝 Content Validation</div>
                                <div class="hero-card-desc">
                                    Validate link integrity, structured data, and metadata completeness
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 16 Tests Overview -->
    <section class="py-5 bg-white" id="test">
        <div class="container px-3 my-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3" style="font-size:1.8rem">16 Professional Web Tests</h2>
                <p class="text-muted mb-4" style="font-size:1rem">We integrate industry-standard tools to measure overall web quality.</p>
            </div>

            <div class="row g-4">
                <!-- Performance Tests -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="fw-bold text-warning mb-3">⚡ Performance</h4>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/performance/speed') }}" class="text-decoration-none text-dark">Global
                                    Speed</a>
                            </h5>
                            <a href="{{ url('/') }}/performance/speed" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Measure load time across 8 global regions</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/performance/load') }}" class="text-decoration-none text-dark">Load
                                    Test</a>
                            </h5>
                            <a href="{{ url('/') }}/performance/load" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Measure concurrent user load with k6</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/performance/mobile') }}" class="text-decoration-none text-dark">Mobile
                                    Performance</a>
                            </h5>
                            <a href="{{ url('/') }}/performance/mobile" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Rendering performance across various devices</p>
                    </div>
                </div>

                <!-- Security Tests -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="fw-bold text-danger mb-3">🔒 Security</h4>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/ssl') }}" class="text-decoration-none text-dark">SSL
                                    Basics</a>
                            </h5>
                            <a href="{{ url('/') }}/security/ssl" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">SSL certificate and basic security settings</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/sslyze') }}" class="text-decoration-none text-dark">SSL
                                    Deep Dive</a>
                            </h5>
                            <a href="{{ url('/') }}/security/sslyze" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">In‑depth TLS analysis via SSLyze</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/headers') }}" class="text-decoration-none text-dark">Security
                                    Headers</a>
                            </h5>
                            <a href="{{ url('/') }}/security/headers" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Validate HSTS, CSP, and CORS policies</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/scan') }}" class="text-decoration-none text-dark">Vulnerability
                                    Scan</a>
                            </h5>
                            <a href="{{ url('/') }}/security/scan" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Dynamic vulnerability detection with OWASP ZAP</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/nuclei') }}" class="text-decoration-none text-dark">Latest
                                    Vulnerabilities</a>
                            </h5>
                            <a href="{{ url('/') }}/security/nuclei" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Pattern‑based CVE checks via Nuclei</p>
                    </div>
                </div>

                <!-- Quality Tests -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="fw-bold text-success mb-3">✨ Quality</h4>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/lighthouse') }}" class="text-decoration-none text-dark">Overall
                                    Quality</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/lighthouse" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Lighthouse: Performance + SEO + Accessibility + PWA</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/accessibility') }}"
                                    class="text-decoration-none text-dark">Accessibility Deep Dive</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/accessibility" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Detailed checks with axe‑core (WCAG 2.1)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/compatibility') }}"
                                    class="text-decoration-none text-dark">Browser Compatibility</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/compatibility" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Rendering tests with Playwright’s 3 engines</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/visual') }}" class="text-decoration-none text-dark">
                                    Responsive UI
                                </a>
                            </h5>
                            <a href="{{ url('/') }}/quality/visual" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Responsive UI checks across key viewports</p>
                    </div>
                </div>

                <!-- Content Tests -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="fw-bold mb-3" style="color: #6f42c1;">📝 Content</h4>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/links') }}" class="text-decoration-none text-dark">Link
                                    Validation</a>
                            </h5>
                            <a href="{{ url('/') }}/content/links" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Broken links and redirect chains</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/structure') }}" class="text-decoration-none text-dark">Structured
                                    Data</a>
                            </h5>
                            <a href="{{ url('/') }}/content/structure" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Schema.org markup validity</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/crawl') }}" class="text-decoration-none text-dark">Site
                                    Crawling</a>
                            </h5>
                            <a href="{{ url('/') }}/content/crawl" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Bulk quality scan across all pages</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/meta') }}" class="text-decoration-none text-dark">Metadata</a>
                            </h5>
                            <a href="{{ url('/') }}/content/meta" class="badge-new">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Validate Open Graph tags and Twitter cards</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PSQC Overview Section -->
    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container my-5">
            <div class="mb-5 text-center">
                <h2 class="fw-bold text-dark" style="font-size:1.8rem">Four Core PSQC Areas</h2>
                <p class="text-muted" style="font-size:1rem">Scientifically assess every aspect of your website.</p>
            </div>

            <div class="row g-4">
                <!-- Performance -->
                <div class="col-md-6 col-lg-3">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #ff6b35, #f7931e);">⚡
                            </div>
                            <h3 class="fw-bold mb-3 ms-3">Performance<br><small class="text-muted">Performance</small></h3>
                        </div>
                        <p class="text-muted mb-3">
                            • Speed across 8 global regions<br>
                            • Realistic load simulations<br>
                            • Responsiveness on mobile and desktop
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-orange-lt text-orange-lt-fg">3 tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">300 points</span>
                        </div>
                    </div>
                </div>

                <!-- Security -->
                <div class="col-md-6 col-lg-3">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #dc3545, #c82333);">🔒</div>
                            <h3 class="fw-bold mb-3 ms-3">Security<br><small class="text-muted">Security</small></h3>
                        </div>
                        <p class="text-muted mb-3">
                            • SSL certificates and vulnerability checks<br>
                            • Tests aligned to OWASP Top 10<br>
                            • Security headers and policy validation
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-red-lt text-red-lt-fg">5 tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">300 points</span>
                        </div>
                    </div>
                </div>

                <!-- Quality -->
                <div class="col-md-6 col-lg-3">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">✨</div>
                            <h3 class="fw-bold mb-3 ms-3">Quality<br><small class="text-muted">Quality</small></h3>
                        </div>
                        <p class="text-muted mb-3">
                            • Lighthouse‑based quality checks<br>
                            • WCAG accessibility and compatibility
                            • SEO and PWA readiness
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-green-lt text-green-lt-fg">4 tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">250 points</span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="col-md-6 col-lg-3">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #6f42c1, #845ef7);">📝</div>
                            <h3 class="fw-bold mb-3 ms-3">Content<br><small class="text-muted">Content</small></h3>
                        </div>
                        <p class="text-muted mb-3">
                            • Check broken links and metadata<br>
                            • Structured data and SEO analysis<br>
                            • Full‑site crawl to uncover hidden issues
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-purple-lt text-purple-lt-fg">4 tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">150 points</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="py-5 bg-white" id="pricing">
        <div class="container px-3 my-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-2" style="font-size:1.8rem">Transparent, Flexible Pricing</h2>
                <p class="text-muted mb-0" style="font-size:1rem">From free trials to certificates — choose a plan that fits your usage.</p>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-nowrap">
                    <thead>
                        <tr>
                            <th style="width:15%">Type</th>
                            <th style="width:20%">Plan</th>
                            <th style="width:15%">Price</th>
                            <th style="width:20%">Limits</th>
                            <th style="width:20%">Key Benefits</th>
                            <th style="width:10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Free Trial -->
                        <tr>
                            <td rowspan="2"><span class="fw-bold">Free Trial</span></td>
                            <td>Guest</td>
                            <td class="text-primary fw-bold">Free</td>
                            <td>5 per month per IP</td>
                            <td>Run single tests, preview basic reports</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Sign up</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Free Member</td>
                            <td class="text-primary fw-bold">Free</td>
                            <td>20 per month per account</td>
                            <td>Try all categories, save history + domain DB</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Sign up</a>
                                @endguest
                            </td>
                        </tr>

                        <!-- Subscription Plans -->
                        <tr>
                            <td rowspan="3"><span class="fw-bold">Subscriptions</span></td>
                            <td>Starter</td>
                            <td>$29/mo</td>
                            <td>Up to 600/month · 60/day</td>
                            <td>Member benefits + <strong>Email alerts</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/') }}/client/purchase?plan=starter"
                                        class="text-primary small fw-semibold">Subscribe</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Pro</td>
                            <td>$69/mo</td>
                            <td>Up to 1,500/month · 150/day</td>
                            <td>Starter + <strong>scheduled scans + scheduler</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/') }}/client/purchase?plan=pro"
                                        class="text-primary small fw-semibold">Subscribe</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Agency</td>
                            <td>$199/mo</td>
                            <td>Up to 6,000/month · 600/day</td>
                            <td>Pro + <strong>white‑label reports</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/') }}/client/purchase?plan=agency"
                                        class="text-primary small fw-semibold">Subscribe</a>
                                @endguest
                            </td>
                        </tr>

                        <!-- Coupon Plans -->
                        <tr>
                            <td rowspan="4"><span class="fw-bold">Coupon‑based</span></td>
                            <td>Test1</td>
                            <td>$4.90</td>
                            <td>Up to 30 runs within 1 day</td>
                            <td>Quota within period, <strong>non‑refundable</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test1') }}"
                                        class="text-primary small fw-semibold">Buy</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Test7</td>
                            <td>$19</td>
                            <td>Up to 150 runs within 7 days</td>
                            <td>Quota within period, <strong>full refund within 3 days before use</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test7') }}"
                                        class="text-primary small fw-semibold">Buy</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Test30</td>
                            <td>$39</td>
                            <td>Up to 500 runs within 30 days</td>
                            <td>Quota within period, <strong>full refund within 7 days before use</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test30') }}"
                                        class="text-primary small fw-semibold">Buy</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Test90</td>
                            <td>$119</td>
                            <td>Up to 1,300 runs within 90 days</td>
                            <td>Quota within period, <strong>full refund within 30 days before use</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test90') }}"
                                        class="text-primary small fw-semibold">Buy</a>
                                @endguest
                            </td>
                        </tr>

                        <!-- Certificates -->
                        <tr>
                            <td rowspan="2"><span class="fw-bold">Certificates</span></td>
                            <td>Individual</td>
                            <td class="text-success fw-bold">$19</td>
                            <td>Single test</td>
                            <td><strong>Test method + Raw Data</strong>, <strong>QR authenticity</strong>, PDF/lookup URL</td>
                            <td class="text-end">
                                <a href="{{ url('/certificate') }}" class="text-primary small fw-semibold">Learn more</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Comprehensive (PSQC)</td>
                            <td class="text-success fw-bold">$59</td>
                            <td>Comprehensive evaluation</td>
                            <td><strong>Requires all individual test data</strong>, aggregates <strong>best score in last 3 days</strong>, Raw Data, QR verification</td>
                            <td class="text-end">
                                <a href="{{ url('/certificate') }}" class="text-primary small fw-semibold">Learn more</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Refund Notes -->
            <div class="text-muted small mt-2">
                * Subscriptions are <strong>fully refundable within 7 days before first use</strong>. <strong>No refunds once any scan has run</strong>.<br>
                * Usage is limited by whichever comes first: <strong>monthly or daily quota</strong>.
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5" style="background-color: #f8f9fa;">
        <div class="container px-3 my-5">
            <div class="text-center">
                <h2 class="fw-bold mb-3" style="font-size:1.8rem">Frequently Asked Questions (FAQ)</h2>
                <p class="text-muted mb-4" style="font-size:1rem">
                    A quick overview of common questions about Web-PSQC
                </p>
            </div>

            <div class="card">
                <div class="accordion accordion-flush" id="accordion-flush">
                    <!-- Q0 -->
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-0-flush" aria-expanded="true">
                                What is Web-PSQC?
                                <div class="accordion-button-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div id="collapse-0-flush" class="accordion-collapse collapse show"
                            data-bs-parent="#accordion-flush">
                            <div class="accordion-body">
                                Web-PSQC is a PSQC‑based platform that comprehensively assesses your website’s Performance, Security, Quality, and Content.
                                It integrates 16 professional, industry‑standard tests and provides <strong>detailed test methods with raw data</strong>,
                                along with <strong>certificates verifiable via QR code</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Q1 -->
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-1-flush" aria-expanded="false">
                                How is it different from other tools?
                                <div class="accordion-button-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div id="collapse-1-flush" class="accordion-collapse collapse"
                            data-bs-parent="#accordion-flush">
                            <div class="accordion-body">
                                1) Runs multiple standard tools <strong>in one go</strong> for a holistic diagnosis · 2) Reflects <strong>global network characteristics</strong> ·
                                3) Improvement guidance based on <strong>real user impact</strong> ·
                                4) Transparent certificates including <strong>procedure, raw data, and QR verification</strong> ·
                                5) Auto‑generated reports with optional expert comments.
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-2-flush" aria-expanded="false">
                                How can I use the results?
                                <div class="accordion-button-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div id="collapse-2-flush" class="accordion-collapse collapse"
                            data-bs-parent="#accordion-flush">
                            <div class="accordion-body">
                                1) Prioritize improvements · 2) Report to clients and executives · 3) Benchmark against competitors ·
                                4) Demonstrate technical capability in proposals and grants · 5) Establish internal quality standards.
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-3-flush" aria-expanded="false">
                                What does it take to earn an A+ grade?
                                <div class="accordion-button-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div id="collapse-3-flush" class="accordion-collapse collapse"
                            data-bs-parent="#accordion-flush">
                            <div class="accordion-body">
                                A total PSQC score of <strong>900+ (out of 1000)</strong> earns an A+ grade.
                                This aligns with <strong>web quality standards pursued by leading tech companies</strong>,
                                designed to ensure <strong>great products don’t lose conversions due to performance or security issues</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-4-flush" aria-expanded="false">
                                How long do tests take?
                                <div class="accordion-button-toggle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div id="collapse-4-flush" class="accordion-collapse collapse"
                            data-bs-parent="#accordion-flush">
                            <div class="accordion-body">
                                Individual tests usually take <strong>30 seconds to 3 minutes</strong>. The suite includes a <strong>k6 load test</strong>.
                                A <strong>comprehensive run (all 16 tests for one domain)</strong> typically takes <strong>30–60 minutes</strong>.
                                You’ll receive an <strong>email notification</strong> upon completion and can review results and reports in the dashboard.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@section('js')
@endsection
