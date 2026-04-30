<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0a">
    <title>{{ $title ?? 'Terminal Learning System' }}</title>
    @livewireStyles
    @vite(['resources/css/terminal.css', 'resources/js/app.js'])
</head>
<body class="terminal-bg">
    {{ $slot }}
    @livewireScripts
</body>
</html>
