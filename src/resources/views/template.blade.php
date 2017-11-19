<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-MLK48JC');</script>
  <!-- End Google Tag Manager -->

  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="{{ config('const.app.description') }}">
  <meta name="keywords" content="{{ implode(', ', config('const.app.keywords')) }}">
  <meta name="author" content="{{ config('const.app.author') }}">

  <meta property="og:title" content="{{ config('const.app.name') }}">
  <meta property="og:type" content="{{ config('const.app.type') }}">
  <meta property="og:url" content="{{ route('index') }}">
  <meta property="og:image" content="{{ route('index') }}{{ config('const.app.image') }}">
  <meta property="og:site_name" content="{{ config('const.app.name') }}">
  <meta property="og:description" content="{{ config('const.app.description') }}">

  <meta name="twitter:card" content="{{ config('const.app.twittercard') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') | {{ config('const.app.name') }}</title>
  <link rel="canonical" href="{{ route('index') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="shortcut icon" href="{{ route('index') }}{{ config('const.app.favicon') }}" type="image/vnd.microsoft.ico"/>
  <style>
    body { padding-top: 70px; }
    .highlight {background-color: #ff0; }
  </style>
</head>
<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLK48JC"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  @include('header')

  <div class="container">
@yield('content')
  </div>
  <script src="{{ asset('js/app.js') }}"></script>
@yield('script')
</body>
</html>
