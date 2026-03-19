@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found'))

@section('illustration')
    <div class="mx-auto w-32 text-zinc-400 dark:text-zinc-500" aria-hidden="true">
        <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full">
            <path d="M56 36L36 56M36 36l20 20" />
            <circle cx="48" cy="48" r="32" />
            <path d="M32 32L24 24M64 64l8 8M64 32l8-8M32 64l-8 8" stroke-width="1" opacity="0.6" />
        </svg>
    </div>
@endsection

@section('action')
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 shadow-sm transition hover:border-zinc-400 hover:bg-zinc-50 dark:hover:border-zinc-500 dark:hover:bg-zinc-800">
        {{ __('Go home') }}
    </a>
@endsection
