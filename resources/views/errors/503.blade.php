@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable'))

@section('illustration')
    <div class="mx-auto w-32 text-zinc-400 dark:text-zinc-500" aria-hidden="true">
        <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full">
            <path d="M48 16v64M48 16c-12 0-22 8-22 20s10 20 22 20 22-8 22-20-10-20-22-20z" />
            <path d="M48 56c12 0 22-8 22-20S60 16 48 16" opacity="0.5" />
            <circle cx="48" cy="48" r="3" />
        </svg>
    </div>
@endsection

@section('action')
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 shadow-sm transition hover:border-zinc-400 hover:bg-zinc-50 dark:hover:border-zinc-500 dark:hover:bg-zinc-800">
        {{ __('Go home') }}
    </a>
@endsection
