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
    <meta property="og:description"
        content="Diagnose your web across Performance, Security, Quality, and Content with 16 tests — and get an A+ grade certificate." />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="{{ App\Models\Setting::first()->og_image }}" />
    <meta property="og:image:alt" content="Web-PSQC Home Preview" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Web-PSQC – Comprehensive Web Quality Testing" />
    <meta name="twitter:description"
        content="Global performance, security, quality, and content in one place. 16 tests with certificate issuance." />
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
            font-size: 1rem;
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

        .hero-url-input {
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(4px);
        }

        .hero-url-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .hero-url-input:focus {
            border-color: #845ef7;
            box-shadow: 0 0 0 0.2rem rgba(132, 94, 247, 0.25);
            background: rgba(255, 255, 255, 0.15);
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
                <!-- Left text area -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <!-- Desktop title -->
                    <h1 class="hero-title d-none d-md-block mb-4">
                        Achieve Top 2% Web Quality<br>Trusted by Global Standards
                    </h1>
                    <!-- Mobile title -->
                    <h2 class="h1 d-md-none mb-4">
                        Achieve Top 2% Web Quality<br>Trusted by Global Standards
                    </h2>

                    <!-- Desktop subtitle -->
                    <p class="hero-subtitle d-none d-md-block mb-4">
                        From Google ranking to user experience and security —<br>
                        diagnose, measure, and improve in just one scan.<br>
                        Web-PSQC delivers a full-spectrum analysis across<br>
                        <strong>Performance · Security · Quality · Content</strong>.
                    </p>

                    <!-- Mobile subtitle -->
                    <p class="hero-subtitle d-md-none mb-4">
                        Google ranking, UX, and security — all in one scan.<br>
                        Web-PSQC runs a complete analysis of<br>
                        <strong>Performance · Security · Quality · Content</strong>.
                    </p>

                    <!-- Replace existing CTA buttons with this -->
                    <div class="input-group" style="max-width: 500px;">
                        <input type="url" class="form-control hero-url-input"
                            placeholder="Enter your URL.." id="speedTestUrl"
                            style="font-size: 1rem; padding: 0.75rem;color:rgb(233, 233, 233)">
                        <button class="btn hero-cta-btn" type="button" onclick="startSpeedTest()"
                            style="padding: 0.75rem 1rem;">
                            Speed Test
                        </button>
                    </div>
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
                                <div class="hero-card-title">⚡ Performance Insights</div>
                                <div class="hero-card-desc">
                                    Benchmark global speed, load capacity, and real-world mobile performance
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-card">
                                <div class="hero-card-title">🔒 Security Assurance</div>
                                <div class="hero-card-desc">
                                    Advanced SSL diagnostics, header checks, and vulnerability scans in one suite
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-card mt-5">
                                <div class="hero-card-title">✨ Quality Compliance</div>
                                <div class="hero-card-desc">
                                    Validate SEO, accessibility, cross-browser standards, and UI regression accuracy
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-card">
                                <div class="hero-card-title">📝 Content Integrity</div>
                                <div class="hero-card-desc">
                                    Ensure link health, metadata completeness, and structured data consistency
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
                <p class="text-muted mb-4" style="font-size:1rem">
                    Industry-standard tooling, integrated into a single PSQC workflow to measure overall web quality.
                </p>
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
                            <a href="{{ url('/') }}/performance/speed" class="badge-new"
                                aria-label="Run Global Speed test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Measure real-world load time across 8 regions and CDNs</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/performance/load') }}" class="text-decoration-none text-dark">Load
                                    Test</a>
                            </h5>
                            <a href="{{ url('/') }}/performance/load" class="badge-new"
                                aria-label="Run Load Test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Concurrent user capacity and error rate (k6)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/performance/mobile') }}"
                                    class="text-decoration-none text-dark">Mobile Performance</a>
                            </h5>
                            <a href="{{ url('/') }}/performance/mobile" class="badge-new"
                                aria-label="Run Mobile Performance test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Rendering & interaction metrics across key devices</p>
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
                            <a href="{{ url('/') }}/security/ssl" class="badge-new"
                                aria-label="Run SSL Basics test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Certificate chain, protocol/cipher, and baseline hygiene</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/sslyze') }}" class="text-decoration-none text-dark">SSL
                                    Deep Dive</a>
                            </h5>
                            <a href="{{ url('/') }}/security/sslyze" class="badge-new"
                                aria-label="Run SSL Deep Dive test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">In-depth TLS configuration analysis (SSLyze)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/header') }}"
                                    class="text-decoration-none text-dark">Security Headers</a>
                            </h5>
                            <a href="{{ url('/') }}/security/header" class="badge-new"
                                aria-label="Run Security Headers test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Validate HSTS, CSP, CORS, X-Frame-Options, and more</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/scan') }}"
                                    class="text-decoration-none text-dark">Vulnerability Scan</a>
                            </h5>
                            <a href="{{ url('/') }}/security/scan" class="badge-new"
                                aria-label="Run Vulnerability Scan">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Dynamic application scanning (OWASP ZAP)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/security/nuclei') }}" class="text-decoration-none text-dark">Latest
                                    Vulnerabilities</a>
                            </h5>
                            <a href="{{ url('/') }}/security/nuclei" class="badge-new"
                                aria-label="Run Latest Vulnerabilities test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Template-based CVE checks and misconfig detection (Nuclei)</p>
                    </div>
                </div>

                <!-- Quality Tests -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="fw-bold text-success mb-3">✨ Quality</h4>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/lighthouse') }}"
                                    class="text-decoration-none text-dark">Lighthouse Audit</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/lighthouse" class="badge-new"
                                aria-label="Run Lighthouse Audit">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Performance · SEO · Accessibility · PWA (Lighthouse)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/accessibility') }}"
                                    class="text-decoration-none text-dark">Accessibility Deep Dive</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/accessibility" class="badge-new"
                                aria-label="Run Accessibility test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">WCAG-aligned checks with axe-core (2.1/2.2-ready)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/compatibility') }}"
                                    class="text-decoration-none text-dark">Browser Compatibility</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/compatibility" class="badge-new"
                                aria-label="Run Browser Compatibility test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">WebKit · Blink · Gecko rendering checks (Playwright)</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/quality/visual') }}"
                                    class="text-decoration-none text-dark">Responsive UI</a>
                            </h5>
                            <a href="{{ url('/') }}/quality/visual" class="badge-new"
                                aria-label="Run Responsive UI test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Viewport-based layout & visual regression checks</p>
                    </div>
                </div>

                <!-- Content Tests -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="fw-bold mb-3" style="color:#6f42c1;">📝 Content</h4>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/links') }}" class="text-decoration-none text-dark">Link
                                    Validation</a>
                            </h5>
                            <a href="{{ url('/') }}/content/links" class="badge-new"
                                aria-label="Run Link Validation test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Broken links, redirect chains, and 4xx/5xx detection</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/structure') }}"
                                    class="text-decoration-none text-dark">Structured Data</a>
                            </h5>
                            <a href="{{ url('/') }}/content/structure" class="badge-new"
                                aria-label="Run Structured Data test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Schema.org validity and rich-result readiness</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/crawl') }}" class="text-decoration-none text-dark">Site
                                    Crawling</a>
                            </h5>
                            <a href="{{ url('/') }}/content/crawl" class="badge-new"
                                aria-label="Run Site Crawling test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Robots/sitemap validation and bulk page quality scan</p>
                    </div>

                    <div class="test-item">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="fw-bold mb-0 flex-grow-1">
                                <a href="{{ url('/content/meta') }}"
                                    class="text-decoration-none text-dark">Metadata</a>
                            </h5>
                            <a href="{{ url('/') }}/content/meta" class="badge-new"
                                aria-label="Run Metadata test">Test</a>
                        </div>
                        <p class="text-muted small mb-0">Open Graph, Twitter Cards, and canonical hygiene</p>
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
                <p class="text-muted" style="font-size:1rem">
                    Scientifically structured evaluation across <strong>Performance · Security · Quality ·
                        Content</strong>.
                </p>
            </div>

            <div class="row g-4">
                <!-- Performance -->
                <div class="col-md-6">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #ff6b35, #f7931e);">⚡
                            </div>
                            <h3 class="fw-bold mb-3 ms-3">Performance</h3>
                        </div>
                        <p class="text-muted mb-3">
                            • Global speed benchmarks (8 regions)<br>
                            • Load simulation with real concurrency<br>
                            • Mobile & desktop responsiveness
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-orange-lt text-orange-lt-fg">3 Tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">300 Points</span>
                        </div>
                    </div>
                </div>

                <!-- Security -->
                <div class="col-md-6">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #dc3545, #c82333);">🔒
                            </div>
                            <h3 class="fw-bold mb-3 ms-3">Security</h3>
                        </div>
                        <p class="text-muted mb-3">
                            • SSL/TLS validation and certificate checks<br>
                            • OWASP-aligned vulnerability scans<br>
                            • Security headers & policy enforcement
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-red-lt text-red-lt-fg">5 Tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">300 Points</span>
                        </div>
                    </div>
                </div>

                <!-- Quality -->
                <div class="col-md-6">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #28a745, #20c997);">✨
                            </div>
                            <h3 class="fw-bold mb-3 ms-3">Quality</h3>
                        </div>
                        <p class="text-muted mb-3">
                            • Lighthouse-based web quality scores<br>
                            • Accessibility & browser compatibility<br>
                            • SEO and PWA readiness checks
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-green-lt text-green-lt-fg">4 Tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">250 Points</span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="col-md-6">
                    <div class="psqc-card">
                        <div class="d-flex">
                            <div class="psqc-icon" style="background: linear-gradient(135deg, #6f42c1, #845ef7);">📝
                            </div>
                            <h3 class="fw-bold mb-3 ms-3">Content</h3>
                        </div>
                        <p class="text-muted mb-3">
                            • Broken links & metadata validation<br>
                            • Structured data & schema checks<br>
                            • Full-site crawl to detect hidden issues
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-purple-lt text-purple-lt-fg">4 Tests</span>
                            <span class="badge bg-yellow-lt text-yellow-lt-fg ms-1">150 Points</span>
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
                <p class="text-muted mb-0" style="font-size:1rem">
                    From free trials to certificates—choose a plan that fits your usage.
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter table-nowrap align-middle">
                    <thead>
                        <tr>
                            <th style="width:15%">Type</th>
                            <th style="width:20%">Plan</th>
                            <th style="width:15%">Price</th>
                            <th style="width:20%">Usage Limits</th>
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
                            <td>5 runs / month / IP</td>
                            <td>Single tests, preview basic reports</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold"
                                        aria-label="Sign up for Guest free trial">Sign up</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Free Member</td>
                            <td class="text-primary fw-bold">Free</td>
                            <td>20 runs / month / account</td>
                            <td>All categories, save history & domain DB</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Sign up for Free Member">Sign
                                        up</a>
                                @endguest
                            </td>
                        </tr>

                        <!-- Subscription Plans -->
                        <tr>
                            <td rowspan="3"><span class="fw-bold">Subscriptions</span></td>
                            <td>Starter</td>
                            <td>$29 / mo</td>
                            <td>Up to 600 / month · 60 / day</td>
                            <td>Member benefits + <strong>Email alerts</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Starter plan">Buy</a>
                                @else
                                    <a href="{{ url('/') }}/client/purchase?plan=starter"
                                        class="text-primary small fw-semibold"
                                        aria-label="Subscribe to Starter">Subscribe</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Pro
                            </td>
                            <td>$69 / mo</td>
                            <td>Up to 1,500 / month · 150 / day</td>
                            <td>Starter + <strong>scheduled scans</strong> (built-in scheduler)</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Pro plan">Buy</a>
                                @else
                                    <a href="{{ url('/') }}/client/purchase?plan=pro"
                                        class="text-primary small fw-semibold" aria-label="Subscribe to Pro">Subscribe</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Agency</td>
                            <td>$199 / mo</td>
                            <td>Up to 6,000 / month · 600 / day</td>
                            <td>Pro + <strong>white-label reports</strong></td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Agency plan">Buy</a>
                                @else
                                    <a href="{{ url('/') }}/client/purchase?plan=agency"
                                        class="text-primary small fw-semibold"
                                        aria-label="Subscribe to Agency">Subscribe</a>
                                @endguest
                            </td>
                        </tr>

                        <!-- Coupon Plans -->
                        <tr>
                            <td rowspan="4"><span class="fw-bold">Coupon-based</span></td>
                            <td>Test1</td>
                            <td>$4.90</td>
                            <td>Up to 30 runs within 1 day</td>
                            <td>Quota within period</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Test1 coupon">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test1') }}"
                                        class="text-primary small fw-semibold" aria-label="Purchase Test1 coupon">Buy</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Test7</td>
                            <td>$19</td>
                            <td>Up to 150 runs within 7 days</td>
                            <td>Quota within period</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Test7 coupon">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test7') }}"
                                        class="text-primary small fw-semibold" aria-label="Purchase Test7 coupon">Buy</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Test30</td>
                            <td>$39</td>
                            <td>Up to 500 runs within 30 days</td>
                            <td>Quota within period</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Test30 coupon">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test30') }}"
                                        class="text-primary small fw-semibold" aria-label="Purchase Test30 coupon">Buy</a>
                                @endguest
                            </td>
                        </tr>
                        <tr>
                            <td>Test90</td>
                            <td>$119</td>
                            <td>Up to 1,300 runs within 90 days</td>
                            <td>Quota within period</td>
                            <td class="text-end">
                                @guest
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                        class="text-primary small fw-semibold" aria-label="Buy Test90 coupon">Buy</a>
                                @else
                                    <a href="{{ url('/client/purchase?plan=test90') }}"
                                        class="text-primary small fw-semibold" aria-label="Purchase Test90 coupon">Buy</a>
                                @endguest
                            </td>
                        </tr>

                        <!-- Certificates -->
                        <tr>
                            <td rowspan="2"><span class="fw-bold">Certificates</span></td>
                            <td>Individual</td>
                            <td class="text-success fw-bold">$19</td>
                            <td>Single test</td>
                            <td>
                                <strong>Test method + Raw data</strong>, <strong>QR authenticity</strong>, PDF & lookup
                                URL
                            </td>
                            <td class="text-end">
                                <a href="{{ url('/certificate') }}" class="text-primary small fw-semibold"
                                    aria-label="Learn more about Individual certificate">Learn more</a>
                            </td>
                        </tr>
                        <tr>
                            <td>Comprehensive (PSQC)</td>
                            <td class="text-success fw-bold">$59</td>
                            <td>Comprehensive evaluation</td>
                            <td>
                                <strong>Requires all individual test data</strong>,<br>aggregates <strong>best score in
                                    last 3 days</strong>,
                                Raw data, QR verification
                            </td>
                            <td class="text-end">
                                <a href="{{ url('/certificate') }}" class="text-primary small fw-semibold"
                                    aria-label="Learn more about Comprehensive PSQC certificate">Learn more</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Usage Notes -->
            <div class="text-muted small mt-2">
                * Usage is limited by whichever comes first: <strong>monthly or daily quota</strong>. Taxes may apply
                depending on your region.
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5" style="background-color:#f8f9fa;">
        <div class="container px-3 my-5">
            <div class="text-center">
                <h2 class="fw-bold mb-3" style="font-size:1.8rem">Frequently Asked Questions (FAQ)</h2>
                <p class="text-muted mb-4" style="font-size:1rem">
                    Quick answers to the most common questions about Web-PSQC.
                </p>
            </div>

            <div class="card">
                <div class="accordion accordion-flush" id="faq-accordion">
                    <!-- Q0 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq-h0">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-c0" aria-expanded="true" aria-controls="faq-c0">
                                What is Web-PSQC?
                                <span class="ms-auto accordion-button-toggle" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </span>
                            </button>
                        </h3>
                        <div id="faq-c0" class="accordion-collapse collapse show" aria-labelledby="faq-h0"
                            data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                Web-PSQC is a unified testing platform that evaluates your website across
                                <strong>Performance, Security, Quality, and Content</strong>.
                                It integrates <strong>16 professional, industry-standard tests</strong>, provides
                                <strong>transparent test methods and raw data</strong>, and issues
                                <strong>QR-verifiable certificates</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Q1 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq-h1">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-c1" aria-expanded="false" aria-controls="faq-c1">
                                How is it different from other tools?
                                <span class="ms-auto accordion-button-toggle" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </span>
                            </button>
                        </h3>
                        <div id="faq-c1" class="accordion-collapse collapse" aria-labelledby="faq-h1"
                            data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                1) Runs <strong>multiple standard tools in a single pass</strong> for a holistic view ·
                                2) Reflects <strong>global network conditions</strong> (multi-region) ·
                                3) Provides <strong>action-ready guidance</strong> tied to user impact ·
                                4) Offers <strong>transparent certificates</strong> (procedure, raw data, QR) ·
                                5) <strong>Auto-generated reports</strong> with optional expert comments.
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq-h2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-c2" aria-expanded="false" aria-controls="faq-c2">
                                How can I use the results?
                                <span class="ms-auto accordion-button-toggle" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </span>
                            </button>
                        </h3>
                        <div id="faq-c2" class="accordion-collapse collapse" aria-labelledby="faq-h2"
                            data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                1) Prioritize fixes by impact · 2) Report to clients/executives ·
                                3) Benchmark against competitors · 4) Support proposals and grants ·
                                5) Establish internal <strong>quality SLAs and standards</strong>.
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq-h3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-c3" aria-expanded="false" aria-controls="faq-c3">
                                What does it take to earn an A+ grade?
                                <span class="ms-auto accordion-button-toggle" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </span>
                            </button>
                        </h3>
                        <div id="faq-c3" class="accordion-collapse collapse" aria-labelledby="faq-h3"
                            data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                A total PSQC score of <strong>900+</strong> (out of 1,000) qualifies for an A+.
                                The threshold aligns with <strong>best-in-class web quality practices</strong> to ensure
                                high-performing, secure experiences that protect conversions.
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq-h4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq-c4" aria-expanded="false" aria-controls="faq-c4">
                                How long do tests take?
                                <span class="ms-auto accordion-button-toggle" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6l6 -6" />
                                    </svg>
                                </span>
                            </button>
                        </h3>
                        <div id="faq-c4" class="accordion-collapse collapse" aria-labelledby="faq-h4"
                            data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                Individual tests usually take <strong>30 seconds–3 minutes</strong> (includes a
                                <strong>k6 load test</strong>).
                                A <strong>full run (all 16 tests for one domain)</strong> typically takes <strong>30–60
                                    minutes</strong>.
                                You’ll receive an <strong>email notification</strong> when results are ready; all
                                reports are saved to your dashboard.
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>
@section('js')
    <script>
        function startSpeedTest() {
            const urlInput = document.getElementById('speedTestUrl');
            const url = urlInput.value.trim();

            if (!url) {
                urlInput.focus();
                return;
            }

            // URL 형식 간단 검증
            if (!url.includes('.')) {
                alert('올바른 URL을 입력해주세요 (예: example.com)');
                return;
            }

            // https:// 자동 추가
            let formattedUrl = url;
            if (!url.startsWith('http://') && !url.startsWith('https://')) {
                formattedUrl = 'https://' + url;
            }

            // 페이지 이동
            window.location.href = `/performance/speed?url=${encodeURIComponent(formattedUrl)}&start=true`;
        }

        // Enter 키 지원
        document.getElementById('speedTestUrl').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                startSpeedTest();
            }
        });
    </script>
@endsection
