@php
    $setting = \App\Models\Setting::first();
@endphp
<title>{{ $setting->seo_title }}</title>
<meta name="description" content="{{ $setting->seo_description }}">
<meta name="keywords" content="{{ $setting->seo_keywords }}">
<meta name="author" content="{{ $setting->seo_author }}">

<meta property="og:url" content="{{ $setting->og_url }}" />
<meta property="og:type" content="{{ $setting->og_type }}" />
<meta property="og:title" content="{{ $setting->og_title }}" />
<meta property="og:description" content="{{ $setting->og_description }}" />
<meta property='og:image' content='{{ url('/') }}/storage/{{ $setting->og_image }}' />
