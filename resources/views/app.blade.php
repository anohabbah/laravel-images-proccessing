<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <link href="{{ mix('assets/css/app.css') }}" type="text/css" rel="stylesheet">
        <script src="{{ mix('assets/js/app.js') }}" defer></script>
    </head>
    <body class="bg-gray-200">
        <div id="app">
            <div class="container mx-auto px-4 py-8">
                @yield('body')
            </div>
        </div>
    </body>
</html>
