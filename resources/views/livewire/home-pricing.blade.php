@section('title')
    <title>💳 Pricing & Subscriptions – Free, Plans, Coupons, Certificates | Web-PSQC</title>
    <meta name="description"
        content="DevTeam Test 요금·구독 안내: 비회원/무료 회원 체험, Starter/Pro/Agency 구독, 1~90일 쿠폰, Report1(개별 인증서)·ReportFull(PSQC 종합 인증서) 발급. QR 진위 검증·PDF/이메일 제공.">
    <meta name="keywords"
        content="DevTeam Test 요금제, 웹사이트 테스트 가격, 구독 플랜, 쿠폰, 인증서, PSQC, 성능 보안 품질 콘텐츠, 웹 성능 테스트, 리포트, 화이트라벨 리포트">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Pricing & Subscriptions – Free, Plans, Coupons, Certificates" />
    <meta property="og:description" content="From free trials to Starter/Pro/Agency, short-term coupons, and individual/comprehensive certificates. Supports QR verification and PDF issuance." />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="{{ App\Models\Setting::first()->og_image }}" />
    <meta property="og:image:alt" content="DevTeam Test 요금 및 구독안내" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="요금 및 구독안내 – 무료·구독·쿠폰·인증서 | DevTeam Test" />
    <meta name="twitter:description" content="무료·구독·쿠폰·인증서 구성과 혜택을 한눈에. QR 검증 및 PDF/이메일 발급 지원." />
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
            'description' => '월 최대 600회 · 일 최대 60회, 개인 프로젝트 적합, 이메일 알림 포함',
            'url' => url('/client/purchase?plan=starter'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Pro (monthly)',
            'price' => 69,
            'priceCurrency' => 'USD',
            'category' => 'Subscription',
            'description' => '월 최대 1,500회 · 일 최대 150회, 검사 예약 및 스케줄러 주기 검사',
            'url' => url('/client/purchase?plan=pro'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Agency (monthly)',
            'price' => 199,
            'priceCurrency' => 'USD',
            'category' => 'Subscription',
            'description' => '월 최대 6,000회 · 일 최대 600회, 다수 도메인/고객 관리, 화이트라벨 리포트',
            'url' => url('/client/purchase?plan=agency'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test1 (coupon)',
            'price' => 4.9,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => '1일 이내 최대 30회, 단기 급테스트, 환불 불가',
            'url' => url('/client/purchase?plan=test1'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test7 (coupon) ',
            'price' => 19,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => '7일 이내 최대 150회, 스프린트 QA, 사용 전 3일 이내 전액 환불',
            'url' => url('/client/purchase?plan=test7'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test30 (coupon)',
            'price' => 39,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => '30일 이내 최대 500회, 프로젝트 안정화, 사용 전 7일 이내 전액 환불',
            'url' => url('/client/purchase?plan=test30'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Test90 (coupon)',
            'price' => 119,
            'priceCurrency' => 'USD',
            'category' => 'Voucher',
            'description' => '90일 이내 최대 1,300회, 릴리즈 대응, 사용 전 30일 이내 전액 환불',
            'url' => url('/client/purchase?plan=test90'),
        ],
        [
            '@type' => 'Offer',
            'name'  => 'Report1 (individual certificate)',
            'price' => 19,
            'priceCurrency' => 'USD',
            'category' => 'Certificate',
            'description' => '단일 테스트 결과 인증서, 시험방법+Raw Data, QR 진위 검증, PDF/이메일 발송',
        ],
        [
            '@type' => 'Offer',
            'name'  => 'ReportFull (PSQC 종합 인증서)',
            'price' => 59000,
            'priceCurrency' => 'KRW',
            'category' => 'Certificate',
            'description' => '모든 개별 인증 항목 보유 시 발행, 최근 3일 내 최고 성적 합산, QR 진위 검증, PDF/이메일 발송',
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

        {{-- 헤더 --}}
        <div class="row mb-2">
            <div class="col">
                <h2 class="page-title mb-1">요금 및 구독안내</h2>
                <div class="text-muted small">무료 체험 → 구독 플랜 → 쿠폰형 → 인증서까지, 사용 패턴에 맞춰 선택하세요.</div>
            </div>
        </div>

        {{-- 무료(Free) --}}
        <h3 class="mb-2">무료</h3>
        <div class="row row-cards g-2 mb-2">
            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">비회원</h4>
                            <!-- 불필요 배지 제거 -->
                        </div>
                        <div class="h3 fw-bold mb-1">₩0</div>
                        <div class="text-muted small mb-2">월 5회 (IP 기준)</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 성능/보안/품질/콘텐츠 단일 테스트</li>
                            <li>• 기본 리포트 미리보기</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">회원가입하고 더 사용하기</a>
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
                            <h4 class="card-title mb-0">무료 회원</h4>
                        </div>
                        <div class="h3 fw-bold mb-1">₩0</div>
                        <div class="text-muted small mb-2">월 20회 (계정 기준)</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 모든 테스트 카테고리 체험</li>
                            <li>• 도메인 DB 저장 + 결과 히스토리 저장</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">시작하기</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        {{-- 구독(Subscription) --}}
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
        <h3 class="mb-2">구독</h3>
        <div class="row row-cards g-2 mb-2">
            <div class="col-md-4">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h4 class="card-title mb-0">Starter</h4>
                        </div>
                        <div class="h3 fw-bold mb-1">$29 <span class="text-muted h6">/mo</span></div>
                        <div class="text-muted small mb-2">월 최대 600회 · 일 최대 60회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 개인 프로젝트 적합</li>
                            <li>• 기본 회원 혜택 +</li>
                            <li>• <strong>이메일 알림</strong></li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구독하기</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            @if (!$activeSubscription)
                                <a href="{{ url('/') }}/client/purchase?plan=starter"
                                    class="text-primary small fw-semibold">구독하기</a>
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
                        <div class="text-muted small mb-2">월 최대 1,500회 · 일 최대 150회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 중소규모 사업장/에이전시 적합</li>
                            <li>• Starter 회원 혜택 +</li>
                            <li>• <strong>검사 예약</strong> + <strong>스케줄러 주기 검사</strong></li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구독하기</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            @if ($activeSubscription && $activePlanType === 'pro')
                                <span class="text-success small fw-semibold">현재 구독중</span>
                            @elseif(!$activeSubscription)
                                <a href="{{ url('/') }}/client/purchase?plan=pro"
                                    class="text-primary small fw-semibold">구독하기</a>
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
                        <div class="text-muted small mb-2">월 최대 6,000회 · 일 최대 600회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 다수 도메인/고객 관리</li>
                            <li>• Pro 회원 혜택 +</li>
                            <li>• <strong>화이트라벨 리포트(인증서를 로고 수정)</strong></li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구독하기</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            @if (!$activeSubscription)
                                <a href="{{ url('/') }}/client/purchase?plan=agency"
                                    class="text-primary small fw-semibold">구독하기</a>
                                <a href="{{ url('/') }}/client/purchase?plan=agency" class="stretched-link"></a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <div class="text-muted small mb-2">
            * 구독은 <strong>사용 전 7일 이내 전액 환불</strong> 가능하며, <strong>1회라도 검사를 진행하면 환불이 불가</strong>합니다.<br>
            * 구독 서비스는 <strong>월 한도 또는 일 한도 중 먼저 도달하는 기준에 따라 사용이 제한</strong>됩니다.
        </div>

        {{-- 쿠폰(Coupons) --}}
        <h3 class="mb-2">쿠폰</h3>
        <div class="row row-cards g-2 mb-2">
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Test1</h4>
                        <div class="h4 fw-bold mb-1">$4.90</div>
                        <div class="text-muted small mb-2">1일 이내 최대 30회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 단기 급테스트</li>
                            <li>• 환불 불가</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구매</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test1') }}"
                                class="text-primary small fw-semibold">구매</a>
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
                        <div class="text-muted small mb-2">7일 이내 최대 150회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 스프린트 QA</li>
                            <li>• 사용 전 3일 이내 전액 환불 가능</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구매</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test7') }}"
                                class="text-primary small fw-semibold">구매</a>
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
                        <div class="text-muted small mb-2">30일 이내 최대 500회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 프로젝트 안정화</li>
                            <li>• 사용 전 7일 이내 전액 환불 가능</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구매</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test30') }}"
                                class="text-primary small fw-semibold">구매</a>
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
                        <div class="text-muted small mb-2">90일 이내 최대 1,300회</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 릴리즈 대응</li>
                            <li>• 사용 전 30일 이내 전액 환불 가능</li>
                        </ul>
                        @guest
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="text-primary small fw-semibold">구매</a>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#signinModal"
                                class="stretched-link"></a>
                        @else
                            <a href="{{ url('/client/purchase?plan=test90') }}"
                                class="text-primary small fw-semibold">구매</a>
                            <a href="{{ url('/client/purchase?plan=test90') }}" class="stretched-link"></a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
        <div class="text-muted small mb-2">
            * 쿠폰은 기간 내 총량제이며, <strong>7일/30일/90일권은 사용 전, 명시된 기간 안에 전액 환불 가능</strong>합니다.<br>
            * <strong>환불 가능 기간 이후 또는 1회라도 사용 시 환불이 불가</strong>합니다.
        </div>

        {{-- 인증서(Certificates) --}}
        <h3 class="mb-2">인증서</h3>
        <div class="row row-cards g-2">
            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">Report1 (개별 인증서)</h4>
                        <div class="h4 fw-bold mb-1">$19</div>
                        <div class="text-muted small mb-2">단일 테스트 결과 인증서</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• 시험방법 + <strong>Raw Data</strong> 포함</li>
                            <li>• <strong>QR 코드</strong>로 진위 검증</li>
                            <li>• PDF 다운로드 / 이메일 발송</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 position-relative">
                    <div class="card-body p-3">
                        <h4 class="card-title mb-1">ReportFull (종합 인증서)</h4>
                        <div class="h4 fw-bold mb-1">$59</div>
                        <div class="text-muted small mb-2">PSQC 종합 점수 인증</div>
                        <ul class="list-unstyled small mb-2">
                            <li>• <strong>모든 개별 인증 항목</strong> 검사 데이터가 있을 때 발행</li>
                            <li>• 최근 <strong>3일 이내</strong>의 결과 중 <strong>최고 성적</strong>으로 합산</li>
                            <li>• 시험방법 + Raw Data, <strong>QR 진위 검증</strong></li>
                            <li>• PDF 다운로드 / 이메일 발송</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-muted small mt-2">
            * 인증서는 결제 후 즉시 발급되며 환불 불가입니다.
        </div>

    </div>
</div>

@section('js')
@endsection
