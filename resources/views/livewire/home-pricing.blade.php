@section('title')
    <title>💳 Pricing & Subscriptions – Free, Plans, Coupons, Certificates | Web-PSQC</title>
    <meta name="description"
        content="Web-PSQC pricing: Free guest/member, Starter/Pro/Agency subscriptions, 1–90 day coupons, and certificates (Report1/ReportFull). Includes QR verification and PDF/email delivery.">
    <meta name="keywords"
        content="Web-PSQC pricing, website testing prices, subscription plans, coupons, certificates, PSQC, performance security quality content, web performance testing, reports, white-label reports">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Pricing & Subscriptions – Free, Plans, Coupons, Certificates" />
    <meta property="og:description"
        content="From free trials to Starter/Pro/Agency, short-term coupons, and individual/comprehensive certificates. Supports QR verification and PDF issuance." />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="{{ App\Models\Setting::first()->og_image }}" />
    <meta property="og:image:alt" content="Web-PSQC Pricing & Subscriptions" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Pricing & Subscriptions – Free, Plans, Coupons, Certificates | Web-PSQC" />
    <meta name="twitter:description"
        content="See Free, Subscriptions, Coupons, and Certificates at a glance. Supports QR verification and PDF/email issuance." />
    <meta name="twitter:image" content="{{ App\Models\Setting::first()->og_image }}" />

    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'OfferCatalog',
    'name' => 'Web-PSQC Pricing & Subscriptions',
    'url' => url()->current(),
    'provider' => [
        '@type' => 'Organization',
        'name' => 'DevTeam Co., Ltd.',
        'url' => url('/'),
    ],
    'itemListElement' => [
        [
            '@type' => 'Offer',
            'name'  => 'Starter (monthly)',
            'price' => 29,
            'priceCurrency' => 'USD',
            'category' => 'Subscription',
            'description' => 'Up to 600/month · 60/day, ideal for personal projects, includes email alerts',
            'url' => url('/client/purchase?plan=starter'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Pro (monthly)',
            'price' => 69,
            'priceCurrency' => 'USD',
            'category' => 'Subscription',
            'description' => 'Up to 1,500/month · 150/day, scheduled scans and scheduler',
            'url' => url('/client/purchase?plan=pro'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Agency (monthly)',
            'price' => 199,
            'priceCurrency' => 'USD',
            'category' => 'Subscription',
            'description' => 'Up to 6,000/month · 600/day, manage multiple domains/clients, white‑label reports',
            'url' => url('/client/purchase?plan=agency'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test1 (coupon)',
            'price' => 4.9,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => 'Up to 30 runs within 1 day, short urgent testing, non‑refundable',
            'url' => url('/client/purchase?plan=test1'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test7 (coupon) ',
            'price' => 19,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => 'Up to 150 runs within 7 days, sprint QA, full refund within 3 days before use',
            'url' => url('/client/purchase?plan=test7'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test30 (coupon)',
            'price' => 39,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => 'Up to 500 runs within 30 days, project stabilization, full refund within 7 days before use',
            'url' => url('/client/purchase?plan=test30'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test90 (coupon)',
            'price' => 119,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => 'Up to 1,300 runs within 90 days, release readiness, full refund within 30 days before use',
            'url' => url('/client/purchase?plan=test90'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Report1 (individual certificate)',
            'price' => 19,
            'priceCurrency' => 'USD',
            'category' => 'Certificate',
            'description' => 'Certificate for a single test result: test method + raw data, QR verification, PDF/email delivery',
        ],
        [
            '@type' => 'Offer',
            'name'  => 'ReportFull (PSQC comprehensive certificate)',
            'price' => 59,
            'priceCurrency' => 'USD',
            'category' => 'Certificate',
            'description' => 'Issued when all individual items are available; aggregates best score within last 3 days; QR verification; PDF/email delivery',
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('css')
@endsection

<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')

        {{-- Header --}}
        <div class="row mb-2">
            <div class="col">
                <h2 class="page-title mb-1">Pricing & Subscriptions</h2>
                <div class="text-muted small">From Free → Subscriptions → Coupons → Certificates — choose what fits your
                    usage.</div>
            </div>
        </div>

        {{-- Free --}}
        <h3 class="mb-2">Free</h3>
        <div class="row row-cards g-2 mb-2">
            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">Guest</h4>
                            <!-- No badge needed -->
                        </div>
                        <div class="h3 fw-bold mb-1">$0</div>
                        <div class="text-muted small mb-2">5 per month (per IP)</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Run single tests across Performance/Security/Quality/Content</li>
                            <li>• Preview a basic report</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Sign up to use more</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">Free Member</h4>
                        </div>
                        <div class="h3 fw-bold mb-1">$0</div>
                        <div class="text-muted small mb-2">20 per month (per account)</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Try all test categories</li>
                            <li>• Save domains and view results history</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Get started</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        {{-- Subscriptions --}}
        @php
            $activeSubscription = null;
            $activePlanType = null;
            if (Auth::check()) {
                $activeSubscription = \App\Models\UserPlan::where('user_id', Auth::id())
                    ->subscription()
                    ->active()
                    ->where('end_date', '>', now())
                    ->orderByDesc('end_date')
                    ->first();
                $activePlanType = $activeSubscription ? strtolower($activeSubscription->plan_type) : null;
            }
        @endphp
        <h3 class="mb-2">Subscriptions</h3>
        <div class="row row-cards g-2 mb-2">
            <div class="col-md-4">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">Starter</h4>
                        </div>
                        <div class="h3 fw-bold mb-1">$29 <span class="text-muted h6">/mo</span></div>
                        <div class="text-muted small mb-2">Up to 600/month · 60/day</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Ideal for personal projects</li>
                            <li>• Includes all Free Member benefits</li>
                            <li>• <strong>Email alerts</strong></li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Subscribe</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            @if (!$activeSubscription)
                                <a href="{{ url('/') }}/client/purchase?plan=starter"
                                    class="text-primary small fw-semibold">Subscribe</a>
                                <a href="{{ url('/') }}/client/purchase?plan=starter" class="stretched-link"></a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">Pro</h4>
                        </div>
                        <div class="h3 fw-bold mb-1">$69 <span class="text-muted h6">/mo</span></div>
                        <div class="text-muted small mb-2">Up to 1,500/month · 150/day</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Great for SMBs and agencies</li>
                            <li>• Includes all Starter benefits</li>
                            <li>• <strong>Scheduled scans</strong> + built-in <strong>scheduler</strong></li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Subscribe</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            @if ($activeSubscription && $activePlanType === 'pro')
                                <span class="text-success small fw-semibold">Currently subscribed</span>
                            @elseif(!$activeSubscription)
                                <a href="{{ url('/') }}/client/purchase?plan=pro"
                                    class="text-primary small fw-semibold">Subscribe</a>
                                <a href="{{ url('/') }}/client/purchase?plan=pro" class="stretched-link"></a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">Agency</h4>
                        </div>
                        <div class="h3 fw-bold mb-1">$199 <span class="text-muted h6">/mo</span></div>
                        <div class="text-muted small mb-2">Up to 6,000/month · 600/day</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Manage multiple domains and clients</li>
                            <li>• Includes all Pro benefits</li>
                            <li>• <strong>White-label reports</strong> (custom logo)</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Subscribe</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            @if (!$activeSubscription)
                                <a href="{{ url('/') }}/client/purchase?plan=agency"
                                    class="text-primary small fw-semibold">Subscribe</a>
                                <a href="{{ url('/') }}/client/purchase?plan=agency" class="stretched-link"></a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <div class="text-muted small mb-2">
            * Subscriptions are <strong>fully refundable within 7 days before first use</strong>. <strong>No refunds
                once any scan has run</strong>.<br>
            * Usage is limited by whichever comes first: <strong>monthly or daily quota</strong>.
        </div>

        {{-- Coupons --}}
        <h3 class="mb-2">Coupons</h3>
        <div class="row row-cards g-2 mb-2">
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Test1</h4>
                        <div class="h4 fw-bold mb-1">$4.90</div>
                        <div class="text-muted small mb-2">Up to 30 runs within 1 day</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Short, urgent testing</li>
                            <li>• Non-refundable</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test1') }}"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="{{ url('/client/purchase?plan=test1') }}" class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Test7</h4>
                        <div class="h4 fw-bold mb-1">$19</div>
                        <div class="text-muted small mb-2">Up to 150 runs within 7 days</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Sprint QA</li>
                            <li>• Full refund within 3 days before use</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test7') }}"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="{{ url('/client/purchase?plan=test7') }}" class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Test30</h4>
                        <div class="h4 fw-bold mb-1">$39</div>
                        <div class="text-muted small mb-2">Up to 500 runs within 30 days</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Project stabilization</li>
                            <li>• Full refund within 7 days before use</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test30') }}"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="{{ url('/client/purchase?plan=test30') }}" class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Test90</h4>
                        <div class="h4 fw-bold mb-1">$119</div>
                        <div class="text-muted small mb-2">Up to 1,300 runs within 90 days</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Release readiness</li>
                            <li>• Full refund within 30 days before use</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test90') }}"
                                class="text-primary small fw-semibold">Buy</a>
                            <a href="{{ url('/client/purchase?plan=test90') }}" class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
        <div class="text-muted small mb-2">
            * Coupons are <strong>quota-based within the validity period</strong>. <strong>7/30/90-day coupons are fully
                refundable before use within the stated window</strong>.<br>
            * <strong>No refunds after the refundable window or once used</strong>.
        </div>

        {{-- Certificates --}}
        <h3 class="mb-2">Certificates</h3>
        <div class="row row-cards g-2">
            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Report1 (Individual Certificate)</h4>
                        <div class="h4 fw-bold mb-1">$19</div>
                        <div class="text-muted small mb-2">Certificate for a single test result</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Includes test method + <strong>raw data</strong></li>
                            <li>• <strong>QR-code</strong> authenticity verification</li>
                            <li>• PDF download / Email delivery</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">ReportFull (Comprehensive Certificate)</h4>
                        <div class="h4 fw-bold mb-1">$59</div>
                        <div class="text-muted small mb-2">PSQC overall score certification</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• Issued when <strong>all individual test items</strong> have data</li>
                            <li>• Aggregates the <strong>best score within the last 3 days</strong></li>
                            <li>• Test method + raw data, <strong>QR verification</strong></li>
                            <li>• PDF download / Email delivery</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-muted small mt-2">
            * Certificates are issued immediately after payment and are non-refundable.
        </div>

    </div>
</div>

@section('js')
@endsection
