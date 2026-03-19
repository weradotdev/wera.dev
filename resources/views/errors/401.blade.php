@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized'))

@section('illustration')
    <div class="mx-auto w-32 text-zinc-400 dark:text-zinc-500" aria-hidden="true">
        <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full">
            <rect x="24" y="40" width="48" height="40" rx="4" />
            <path d="M36 40V28a12 12 0 0 1 24 0v12" />
            <circle cx="48" cy="58" r="4" />
        </svg>
    </div>
@endsection

@section('action')
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 shadow-sm transition hover:border-zinc-400 hover:bg-zinc-50 dark:hover:border-zinc-500 dark:hover:bg-zinc-800">
        {{ __('Go home') }}
    </a>
@endsection
