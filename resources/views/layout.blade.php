<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-75900038-5"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-75900038-5');
    </script>


    <meta name="description" content="{{ config('app.description') }}">
    <meta name="keywords" content="{{ implode(', ', config('app.keywords', [])) }}">
    <meta name="author" content="{{ config('app.author') }}">

    <meta property="og:title" content="{{ config('app.name') }}">
    <meta property="og:type" content="{{ config('app.type') }}">
    <meta property="og:url" content="{{ route('pages.index') }}">
    <meta property="og:image" content="{{ route('pages.index') }}{{ config('app.image') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:description" content="{{ config('app.description') }}">

    <meta name="twitter:card" content="{{ config('app.twittercard') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="shortcut icon" href="{{ route('pages.index') }}{{ config('app.favicon') }}" type="image/vnd.microsoft.ico"/>
</head>
<body>
    <header>
        @include('parts.header')
    </header>
    <main>
        @includeWhen($errors->any(), 'parts.errors')

        @yield('content')
    </main>
    <footer>
        @include('parts.footer')
    </footer>
    </div>
</body>
<script src="{{ asset('js/app.js') }}" defer></script>
</html>
