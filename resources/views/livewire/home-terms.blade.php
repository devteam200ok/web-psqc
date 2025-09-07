@section('title')
    <title>📒 서비스 이용약관 | DevTeam Test</title>
    <meta name="description" content="DevTeam Test 서비스 이용약관 페이지입니다. 서비스 이용 시 적용되는 권리와 의무, 책임 및 제한 사항을 확인하세요.">
    <meta name="keywords" content="DevTeam Test 이용약관, Terms of Service, 서비스 약관, 정책, 이용 조건">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="서비스 이용약관 | DevTeam Test" />
    <meta property="og:description" content="DevTeam Test 서비스 이용약관을 확인하세요." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 서비스 이용약관" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="서비스 이용약관 | DevTeam Test" />
    <meta name="twitter:description" content="DevTeam Test 서비스 이용약관을 확인하세요." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'DevTeam Co., Ltd.',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '서비스 이용약관',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
        'url'  => url('/'),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
@section('css')
@endsection
<div class="page page-center">
    <div class="container container-narrow py-4">
        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark">
                @if (Storage::disk('public')->exists('branding/logo_color.svg'))
                    <div class="mt-3">
                        <img src="{{ asset('storage/branding/logo_color.svg') }}" style="max-width:160px">
                    </div>
                @endif
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h3 class="card-title"></h3>
                <div class="markdown">
                    <h1>Terms of Service</h1>
                    {!! App\Models\Setting::first()->terms !!}
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
