@props([
    'title' => 'Young Life Haïti — Jennvi',
    'description' => "Young Life Haïti (Jennvi) aide les jeunes à développer leurs dons, leurs capacités et leurs attitudes pour atteindre leur plein potentiel donné par Dieu.",
])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">

    <link rel="icon" href="{{ asset('images/about/logo2.jpg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|inter:400,500,600" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="bg-white font-[Inter] text-slate-700 antialiased">

    <x-site.header />

    <main>
        {{ $slot }}
    </main>

    <x-site.footer />

</body>
</html>
