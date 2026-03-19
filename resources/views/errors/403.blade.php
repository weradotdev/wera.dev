@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden'))

@section('illustration')
    <div class="mx-auto w-32 text-zinc-400 dark:text-zinc-500" aria-hidden="true">
        <svg viewBox="0 0 96 96" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-full">
            <path d="M48 12L16 28v20c0 22 14 42 32 48 18-6 32-26 32-48V28L48 12z" />
            <path d="M48 12v72" stroke-dasharray="2 3" opacity="0.5" />
        </svg>
    </div>
@endsection

@section('action')
    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 shadow-sm transition hover:border-zinc-400 hover:bg-zinc-50 dark:hover:border-zinc-500 dark:hover:bg-zinc-800">
        {{ __('Go home') }}
    </a>
@endsection
