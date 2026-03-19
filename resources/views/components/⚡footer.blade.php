<?php

use Livewire\Component;

new class extends Component {
    //
};
?>


<footer class="w-full">

    <div class="mx-auto container py-12 lg:py-16">

        <div class="flex flex-col lg:flex-row lg:justify-between gap-12">

            <div class="flex flex-col gap-6 lg:max-w-100">

                <a class="flex items-center gap-2 normal-case!" href="/" wire:navigate>

                    <img src="{{ asset('favicon.png') }}" alt="Wera logo" class="h-12 w-auto">

                    <span class="text-lg lg:text-xl font-semibold text-foreground font-sans tracking-normal">Wera
                    </span>

                </a>

                <p class="text-sm text-foreground/60 leading-relaxed">Enterprise-grade platform: Process documents,
                    build workflows, monitor quality. End-to-end document workflows automation with AI.
                </p>
            </div>

            <div class="flex gap-16 lg:gap-24">

                <nav class="flex flex-col gap-3">

                    <a class="text-sm text-foreground/80 transition-colors hover:text-foreground"
                        href="/solutions">Solutions
                    </a>

                    <a class="text-sm text-foreground/80 transition-colors hover:text-foreground" href="/blog">Blog
                    </a>

                    <a class="text-sm text-foreground/80 transition-colors hover:text-foreground"
                        href="/projects">Projects
                    </a>

                    <a href="/status" wire:navigate
                        class="text-sm text-foreground/80 transition-colors hover:text-foreground">Status Page
                    </a>

                </nav>

                <nav class="flex flex-col gap-3">

                    <a class="text-sm text-foreground/80 transition-colors hover:text-foreground" href="/privacy"
                        wire:navigate>Privacy Policy
                    </a>

                    <a class="text-sm text-foreground/80 transition-colors hover:text-foreground" href="/terms"
                        wire:navigate>Terms of Service
                    </a>

                    <a class="text-sm text-foreground/80 transition-colors hover:text-foreground" href="/cookies">Cookie
                        Policy
                    </a>

                    <a href="#"
                        class="termly-display-preferences text-sm text-foreground/80 transition-colors hover:text-foreground">Consent
                        Preferences
                    </a>

                </nav>

            </div>

        </div>

    </div>

</footer>
