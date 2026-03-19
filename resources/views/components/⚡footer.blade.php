<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<footer class="w-full border-t border-foreground/10">

    <div class="mx-auto container px-6 py-12 lg:py-16">

        <div class="flex flex-col lg:flex-row lg:justify-between gap-12">

            {{-- Brand --}}
            <div class="flex flex-col gap-4 lg:max-w-64">
                <a class="flex items-center gap-2 normal-case!" href="/" wire:navigate>
                    <img src="{{ asset('favicon.png') }}" alt="Wera logo" class="h-10 w-auto">
                    <span class="text-lg font-semibold text-foreground font-sans tracking-normal">Wera</span>
                </a>
                <p class="text-sm text-foreground/60 leading-relaxed">
                    Project management that fits how your team works. Boards, tasks, AI assistance, and guest ticket submission — all in one workspace.
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <a href="https://github.com/weradotdev" target="_blank" rel="noopener noreferrer"
                        class="text-foreground/40 hover:text-foreground transition-colors">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"></path></svg>
                    </a>
                    <a href="https://x.com/weradotdev" target="_blank" rel="noopener noreferrer"
                        class="text-foreground/40 hover:text-foreground transition-colors">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.911-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                    </a>
                </div>
            </div>

            {{-- Nav columns --}}
            <div class="flex flex-wrap gap-12 lg:gap-20">

                <nav class="flex flex-col gap-1 min-w-[130px]">
                    <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/40 mb-3">Product</div>
                    <a class="text-sm text-foreground/70 hover:text-foreground transition-colors py-1"
                        href="{{ route('why') }}" wire:navigate>Why Wera</a>
                    <a class="text-sm text-foreground/70 hover:text-foreground transition-colors py-1"
                        href="{{ route('showcase') }}" wire:navigate>Showcase</a>
                    <a class="text-sm text-foreground/70 hover:text-foreground transition-colors py-1"
                        href="{{ route('download') }}" wire:navigate>Download</a>
                    <a class="text-sm text-foreground/70 hover:text-foreground transition-colors py-1"
                        href="{{ route('contact') }}" wire:navigate>Contact</a>
                </nav>

                <nav class="flex flex-col gap-1 min-w-[130px]">
                    <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/40 mb-3">Legal</div>
                    <a class="text-sm text-foreground/70 hover:text-foreground transition-colors py-1"
                        href="{{ route('privacy') }}" wire:navigate>Privacy Policy</a>
                    <a class="text-sm text-foreground/70 hover:text-foreground transition-colors py-1"
                        href="{{ route('terms') }}" wire:navigate>Terms of Service</a>
                </nav>

            </div>

        </div>

        <div class="mt-12 pt-8 border-t border-foreground/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <p class="text-xs text-foreground/40 font-roboto-mono">
                &copy; {{ date('Y') }} Wera. All rights reserved.
            </p>
            <p class="text-xs text-foreground/30">
                hello@wera.dev
            </p>
        </div>

    </div>

</footer>

