@section('title')
    <title>🔐 Privacy Policy | Web-PSQC</title>
    <meta name="description" content="Web-PSQC Privacy Policy page. Review collected personal information items, usage purposes, retention periods, and protective measures.">
    <meta name="keywords" content="Web-PSQC Privacy Policy, Privacy Protection, Personal Information Processing, Data Retention, Security Policy">
    <meta name="author" content="Web-PSQC">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Privacy Policy | Web-PSQC" />
    <meta property="og:description" content="Review the Web-PSQC Privacy Policy." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC Privacy Policy" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Privacy Policy | Web-PSQC" />
    <meta name="twitter:description" content="Review the Web-PSQC Privacy Policy." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Web-PSQC',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: WebPage --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Privacy Policy',
    'url'  => url()->current(),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
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
                    <h1>Privacy Policy</h1>
                    {!! App\Models\Setting::first()->privacy !!}
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
