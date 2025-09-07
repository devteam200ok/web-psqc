<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="canonical" href="https://www.devteam-app.com/{{ request()->path() != '/' ? request()->path() : '' }}" />
    @yield('title')
    @if (session()->has('css_loaded'))
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.3.2/dist/css/tabler.min.css" />
    @else
        @include('inc.component.theme_css')
        <script>
            fetch('https://cdn.jsdelivr.net/npm/@tabler/core@1.3.2/dist/css/tabler.min.css', {
                mode: 'no-cors',
                cache: 'force-cache'
            }).then(() => {
                console.log('CSS cached');
            }).catch(() => {});
        </script>
        @php
            session(['css_loaded' => '']);
            session()->save();
        @endphp
    @endif
    @yield('css')
</head>

<body>
    {{ $slot }}
    <script src="{{ url('/') }}/theme/dist/js/tabler.min.js?1744816591" defer></script>
    <livewire:scripts />
    @yield('js')
</body>

</html>
