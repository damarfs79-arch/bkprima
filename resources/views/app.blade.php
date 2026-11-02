<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/logo-bkprima-emblem.png" />
    <link rel="apple-touch-icon" href="/logo-bkprima-emblem.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#1d4ed8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="BK Prima" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>BK Prima - SMK PGRI 5 Jember</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/js/app.js'])
    @inertiaHead
  </head>
  <body class="bg-slate-50 text-gray-800 antialiased selection:bg-blue-600 selection:text-white">
    @inertia
  </body>
</html>
