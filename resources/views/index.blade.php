<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>
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
    </body>
</html>
