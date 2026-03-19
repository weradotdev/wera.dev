<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<header class="fixed top-0 z-50 w-full px-3 lg:px-6 transition-all duration-300 pt-[env(safe-area-inset-top)] bg-white">
    <div class="transition-all duration-300">
        <nav class="mx-auto transition-all duration-300 max-w-[1600px] bg-transparent">
            <div class="flex items-center justify-between h-18 transition-all duration-300 px-4 lg:px-20">
                <a class="flex items-center gap-2 !normal-case" href="/" wire:navigate>
                   <img src="{{ asset('favicon.png') }}" alt="Wera logo" class="h-8 w-auto">
                    <span
                        class="text-lg lg:text-xl font-semibold text-foreground font-sans tracking-normal">Wera</span>
                </a>
                <div class="flex-1">
                </div>
                <div class="hidden md:flex items-center gap-2">
                    <a class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm"
                        href="/" wire:navigate>Home</a>
                    <a class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm"
                        href="/why" wire:navigate>Why</a>
                    <a class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm"
                        href="/showcase" wire:navigate>Showcase</a>
                    <div class="w-px h-6 bg-foreground/15 mx-1">
                    </div>
                    <a href="/download" wire:navigate
                        class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider bg-eigenpal-black text-background hover:bg-eigenpal-black/90 h-10 px-4 py-2 text-sm">
                        Download
                        </a>
                </div>
            </div>
            <div class="md:hidden overflow-hidden transition-all duration-300 ease-in-out max-h-0 opacity-0">
                <div class="border-t border-b border-foreground/10 bg-background">
                    <div class="px-4 py-4 flex flex-col gap-2">
                        <div class="mb-2">
                            <div class="text-sm font-medium text-foreground mb-3">Solutions</div>
                            <div class="ml-4 space-y-2">
                                <div class="text-xs font-medium text-foreground/80 mb-1">Industry</div>
                                <div class="ml-2 space-y-1">
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/finance-industry">Finance</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/insurance">Insurance</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/logistics">Logistics</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/manufacturing">Manufacturing</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/legal">Legal</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/healthcare">Healthcare</a>
                                </div>
                                <div class="text-xs font-medium text-foreground/80 mb-1 mt-3">Department</div>
                                <div class="ml-2 space-y-1">
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/operations">Operations</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/finance">Finance</a>
                                    <a class="block text-sm text-foreground/70 hover:text-foreground transition-colors"
                                        href="/solutions/risk">Risk</a>
                                </div>
                            </div>
                        </div>
                        <a class="inline-flex items-center cursor-pointer gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm w-full justify-start"
                            href="/blog">Blog</a>
                        <a class="inline-flex items-center cursor-pointer gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm w-full justify-start"
                            href="/projects">Projects</a>
                        <a href="https://studio.eigenpal.com/sign-in"
                            class="inline-flex items-center cursor-pointer gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono tracking-wider text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm w-full justify-start">Log
                            in</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
