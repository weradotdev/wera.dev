<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        @vite('resources/css/app.css')
    </head>
    <body class="antialiased min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200" role="main">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-16">
            <div class="w-full max-w-sm text-center">
                @hasSection('illustration')
                    @yield('illustration')
                @else
                    <div class="mx-auto w-32 text-zinc-400 dark:text-zinc-500" aria-hidden="true">
                        <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full">
                            <circle cx="48" cy="48" r="32" />
                            <path d="M48 32v32M36 48h24" />
                        </svg>
                    </div>
                @endif

                <p class="mt-8 text-6xl font-light tracking-tighter text-zinc-400 dark:text-zinc-600">
                    @yield('code')
                </p>
                <h1 class="mt-2 text-lg font-medium tracking-tight text-zinc-700 dark:text-zinc-300">
                    @yield('message')
                </h1>
                <div class="mt-10">
                    @hasSection('action')
                        @yield('action')
                    @else
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 shadow-sm transition hover:border-zinc-400 hover:bg-zinc-50 dark:hover:border-zinc-500 dark:hover:bg-zinc-800">
                            {{ __('Go home') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>
