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
    <div class="flex flex-col sm:justify-left items-left pt-6 sm:pt-0 bg-gray-100">
        <div class="bg-verd4 flex-initial flex">
            <a href="/">
                <x-application-logo class="fill-current text-gray-500" />
            </a>
            <h1 class="font-bold w-full text-center text-6xl">P41 Technology</h1>
        </div>
        <div class="bg-verd1">
            <ul class="flex p-2 text-top-center justify-center">
                <a href="{{ route('inici') }}">
                    <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{ __('Inici') }}</li>
                </a>
                @if (Auth::User())                
                @if (Auth::user()->admin == true)
                <a href="{{route('seccions.select')}}">
                    <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{ __('Modificar elements') }}</li>
                </a>
                @endif
                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <a href="{{ route('login') }}">
                        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{ __('Iniciar sessió') }}</li>
                    </a>
                @endif
                    <a href="#">
                        <li class="hover:bg-verd3 rounded-md ml-5 mr-5 p-1">{{ __('contacte') }}</li>
                    </a>

            <div class="pl-10 hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <div> {{ App::currentLocale() }} </div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        @foreach (['ca', 'en', 'es'] as $l)
                            @if ($l != App::currentLocale())
                                <x-dropdown-link :href="url('/lang/' . $l)">
                                    {{ $l }}
                                </x-dropdown-link>
                            @endif
                        @endforeach
                    </x-slot>
                </x-dropdown>
            </div>
        </ul>
        </div>
    </div>
    {{ $slot }}
    </div>
    <footer class="bg-verd4 pb-2">
        <p class="flex justify-center font-bold">{{ __('Informació de contacte') }}</p>
        <div class="flex justify-center">
            <p class="pl-2 pr-2">P41 Technology S.L.</p>
            <p class="pl-2 pr-2">Plaça Major nº5, Roda de Ter</p>
            <p class="pl-2 pr-2">contact@p41t.com</p>
            <p class="pl-2 pr-2">+34 612 34 56 78</p>
        </div>
        <ul class="list-inline flex justify-center">
            <li class="pl-2 pr-2"><x-twitter-logo /></li>
            <li class="pl-2 pr-2"><x-facebook-logo /></li>
            <li class="pl-2 pr-2"><x-instagram-logo /></li>
            <li class="pl-2 pr-2"><x-tiktok-logo /></li>
        </ul>
    </footer>
</body>

</html>
