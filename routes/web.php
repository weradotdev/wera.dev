<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

// Wera website pages (migrated from website/src/routes)
Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/why', 'pages::why')->name('why');
Route::livewire('/terms', 'pages::terms')->name('terms');
Route::livewire('/download', 'pages::download')->name('download');
Route::livewire('/showcase', 'pages::showcase')->name('showcase');
Route::livewire('/contact', 'pages::contact')->name('contact');
Route::livewire('/privacy', 'pages::privacy')->name('privacy');

Route::livewire('embed/{workspace:slug}/{project:slug}/ticket', 'pages::ticket')->name('ticket');

// Scramble API docs (default routes ignored in AppServiceProvider::register())
Route::domain(config('scramble.api_domain'))->group(function () {
    Scramble::registerUiRoute('docs', 'v1');
    Scramble::registerJsonSpecificationRoute('docs/api.json', 'v1');
});
