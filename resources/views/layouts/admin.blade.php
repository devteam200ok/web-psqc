<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Devteam Test - Admin Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ url('/') }}/theme/dist/css/tabler.css?1744816593" rel="stylesheet" />
    <link href="{{ url('/') }}/theme/dist/icon/tabler-icons.min.css?1744816593" rel="stylesheet" />
    <livewire:styles />
    <style>
        #sidebar-menu .ti {
            font-size: 20px;
        }
    </style>
    @yield('css')
</head>

<body class="layout-fluid">
    <script src="{{ url('/') }}/theme/dist/js/tabler-theme.min.js?1744816593"></script>
    <div class="page">
        @include('inc.admin_sidebar')
        <div class="page-wrapper">
            @include('inc.admin_topbar')
            {{ $slot }}
            @include('inc.admin_footer')
        </div>
    </div>
    <script src="{{ url('/') }}/theme/dist/js/tabler.min.js?1744816593" defer></script>
    <livewire:scripts />
    @yield('js')
</body>

</html>
