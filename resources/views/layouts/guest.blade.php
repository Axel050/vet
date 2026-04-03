<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="{{ config('app.url') }}/assets/logo.png" type="image/png">

    <meta property="og:title" content="CumbreVets | Historial de tu mascota">
    <meta property="og:description" content="Lleva el control de las atenciones de tu mascota">
    <meta property="og:image" content="{{ config('app.url') }}/assets/logo.png">


    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:type" content="website">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>

<body class="bg-gray-950 font-sans antialiased">
    @include('sprite-front')

    {{ $slot }}
    @livewireScripts
</body>

</html>
