<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-green-50 antialiased text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-green-100 via-green-50 to-white p-6 md:p-10 dark:from-green-950/50 dark:via-zinc-950 dark:to-zinc-950">
            <div class="flex w-full max-w-md flex-col gap-6">
                <a href="{{ route('inici') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <div class="rounded-2xl border border-green-200/80 bg-white/90 p-6 shadow-xl shadow-green-950/5 backdrop-blur sm:p-8 dark:border-zinc-800 dark:bg-zinc-900/90 dark:shadow-black/20">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
