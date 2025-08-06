<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ASKSEO | Chat App')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <meta name="theme-color" content="#6526DE">

    <!-- Apple-specific for iOS -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ASK SEO CHAT APP">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/image_secure.js') }}"></script>
    @vite('resources/js/app.js')
</head>

<body>
    @yield('content')

    <script>
        const pollingtime = 2000;
    </script>
    <script>
        window.userId = {{ auth()->id() }};
        window.userGroups = {!! auth()->user()->groups->pluck('id') !!};
        
    </script>

    @yield('js')
</body>

</html>