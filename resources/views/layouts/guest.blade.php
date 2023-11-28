<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased m-auto max-w-screen-lg">
        <div class=" min-h-screen flex flex-col sm:justify-left items-left pt-6 sm:pt-0 bg-gray-100">
            <div class="bg-verd4 flex-initial flex" >
                <a href="/" >
                    <x-application-logo class="fill-current text-gray-500" />
                </a>
                <h1 class="font-bold w-full text-center text-6xl">P41 Technology</h1>
            </div>
            <div class="bg-verd1">
                <li class="flex p-2 text-top-center justify-center">
                    <a href="#"><ul class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{ __('Inici') }}</ul></a>
                    <a href="#"><ul class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{__('Iniciar sessió')}}</ul></a>
                    <a href="#"><ul class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{__('contacte')}}</ul></a>
                </li>
            </div>

                {{ $slot }}
        </div>
        <footer class="bg-verd4 pb-2">
            <p class="flex justify-center font-bold">{{__('Informació de contacte')}}</p>
            <div class="flex justify-center">
                <p class="pl-2 pr-2">P41 Technology S.L.</p>
            <p class="pl-2 pr-2">Plaça Major nº5, Roda de Ter</p>
            <p class="pl-2 pr-2">contact@p41t.com</p>
            <p class="pl-2 pr-2">+34 612 34 56 78</p>
            </div>
            <li class="list-inline flex justify-center">
                <ul class="pl-2 pr-2"><x-twitter-logo /></ul>
                <ul class="pl-2 pr-2"><x-facebook-logo /></ul>
                <ul class="pl-2 pr-2"><x-instagram-logo /></ul>
                <ul class="pl-2 pr-2"><x-tiktok-logo /></ul>
            </li>
        </footer>
    </body>
</html>

