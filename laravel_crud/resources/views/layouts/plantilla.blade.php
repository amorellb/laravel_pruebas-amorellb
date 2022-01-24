<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Agenda</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- TAILWIND CND -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<header>
    @include('layouts.navigation')
</header>
@yield('content')
</body>
</html>


{{--Header personalizado--}}
{{--    <div class="relative flex items-top bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">--}}
{{--        @if (Route::has('login'))--}}
{{--            <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">--}}
{{--                @auth--}}
{{--                    <a href="{{ url('/dashboard') }}" class="text-sm text-white dark:text-white underline">Dashboard</a>--}}
{{--                @else--}}
{{--                    <a href="{{ route('login') }}" class="text-sm text-white dark:text-white underline">Log in</a>--}}

{{--                    @if (Route::has('register'))--}}
{{--                        <a href="{{ route('register') }}" class="ml-4 text-sm text-white dark:text-white underline">Register</a>--}}
{{--                    @endif--}}
{{--                @endauth--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        <h1 class="ml-4 text-white dark:text-white text-2xl">Agenda</h1>--}}
{{--        <ul class="list-none ml-5">--}}
{{--            <li>--}}
{{--                <a href="{{route('home')}}"--}}
{{--                   class="{{request()->routeIs('home') ? 'active' : ''}} ml-4 text-sm text-white dark:text-white">Home</a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{route('agenda.index')}}"--}}
{{--                   class="{{request()->routeIs('agenda.*') ? 'active' : ''}} ml-4 text-sm text-white dark:text-white">Agenda</a>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--    </div>--}}
