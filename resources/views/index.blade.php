<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <meta name="description" content="このサイトでは Simutrans Japan, Simutrans的な実験室, Simutrans Addon Portal に投稿されているアドオンをまとめて検索できます。">
        <meta name="keywords" content="Simutrans,Addon,シムトランス,アドオン,pak,pak128,pak128.japan">
        <meta name="author" content="128Na">

        <meta property="og:title" content="{{ config('app.name') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="/">
        <meta property="og:image" content="{{ config('app.image') }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:description" content="このサイトでは Simutrans Japan, Simutrans的な実験室, Simutrans Addon Portal に投稿されているアドオンをまとめて検索できます。">

        <meta name="twitter:card" content="summary">

        <link rel="canonical" href="/">
        <link rel="shortcut icon" href="/favicon.ico" type="image/vnd.microsoft.ico" />

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="dark:bg-gray-900 ">
        @include('header')
        <section class="px-4 lg:px-6 py-2.5 my-2.5">
            <div class="mx-auto max-w-screen-md">
                @include('greeting')
                <livewire:pages />
            </div>
        </section>
        @include('footer')
    </body>
</html>
