<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">

    <title>@yield('meta_title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', config('app.name'))"/>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body {font-family: 'Nunito', sans-serif;}</style>

    @stack('head_meta')
    @stack('head_styles')
    @stack('head_scripts')
</head>
<body>
    <div class="container-fluid">
	    @yield('body')
    </div>
    @stack('body_scripts')
</body>
</html>
