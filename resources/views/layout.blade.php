<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-MLK48JC');</script>
    <!-- End Google Tag Manager -->

    <meta name="description" content="{{ config('app.description') }}">
    <meta name="keywords" content="{{ implode(', ', config('app.keywords', [])) }}">
    <meta name="author" content="{{ config('app.author') }}">

    <meta property="og:title" content="{{ config('app.name') }}">
    <meta property="og:type" content="{{ config('app.type') }}">
    <meta property="og:url" content="{{ route('index') }}">
    <meta property="og:image" content="{{ route('index') }}{{ config('app.image') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:description" content="{{ config('app.description') }}">

    <meta name="twitter:card" content="{{ config('app.twittercard') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <link rel="canonical" href="{{ $canonical_url ?? url()->current() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="shortcut icon" href="{{ route('index') }}{{ config('app.favicon') }}" type="image/vnd.microsoft.ico"/>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLK48JC" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
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
</html>
