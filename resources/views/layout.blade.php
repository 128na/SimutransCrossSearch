<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-MLK48JC');</script>
    <!-- End Google Tag Manager -->

    <meta name="viewport" content="width=device-width,initial-scale=1">
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
    <link rel="canonical" href="{{ route('index') }}">
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
        @yield('content')
    </main>
    <footer>
        <div class="container text-center">
        <p class="text-muted text-center">
            <span>created by <a href="{{ config('app.twitter.url') }}" target="_blank">{{ config('app.twitter.name') }}</a>.</span> /
            <span><a href="{{ config('app.github.url') }}" target="_blank"><i class="fa fa-github" aria-hidden="true"></i> Pull requests are always welcome!</a></span>
        </p>
        </div>
    </footer>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('script')
</body>
</html>
