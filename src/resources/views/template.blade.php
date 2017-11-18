<!DOCTYPE html>
<html lang="ja">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="{{ config('const.app.description') }}">
  <meta name="keywords" content="{{ implode(', ', config('const.app.keywords')) }}">
  <meta name="author" content="{{ config('const.app.author') }}">

  <meta property="og:title" content="{{ config('const.app.name') }}">
  <meta property="og:type" content="{{ config('const.app.type') }}">
  <meta property="og:url" content="{{ route('index') }}">
  <meta property="og:site_name" content="{{ config('const.app.name') }}">
  <meta property="og:description" content="{{ config('const.app.description') }}">

  <meta name="twitter:card" content="{{ config('const.app.twittercard') }}">

  <title>@yield('title') | {{ config('const.app.name') }}</title>
  <link rel="canonical" href="{{ route('index') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <style>
    body { padding-top: 70px; }
    .highlight {background-color: #ff0; }
  </style>
</head>
<body>
  @include('header')

  <div class="container">
@yield('content')
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
@yield('script')
</body>
</html>
