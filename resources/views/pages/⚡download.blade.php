<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<main class="flex min-h-screen flex-col pt-[72px]">
    <section class="relative w-full">
        <div class="mx-auto container px-6">
            <div class="relative">
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="left: calc(50% - 50vw);"></div>
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15"
                    style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15"
                    style="bottom: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0"
                        style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0"
                        style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0"
                        style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0"
                        style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="pt-20 lg:pt-24 pb-12 lg:pb-16 px-4 lg:px-8 flex items-center justify-between gap-8">
                        <div>
                            <div class="mb-4"><span
                                    class="font-roboto-mono text-sm tracking-wide scroll-mt-24 text-foreground/60">/
                                    OPEN SOURCE</span></div>
                            <h1
                                class="text-3xl font-medium leading-tight tracking-tight text-foreground md:text-4xl lg:text-5xl mb-4">
                                Downloads</h1>
                            <p class="text-base text-foreground/60 max-w-2xl">We build and <span
                                    class="underline decoration-eigenpal-green-light underline-offset-4">open-source</span>
                                tools that solve real problems we encounter while building Wera.</p>
                        </div>
                        <div class="hidden lg:block flex-shrink-0" style="width: 240px; height: 258px;">
                            <div class="pointer-events-none flex items-center justify-center "><canvas
                                    class="pointer-events-none" style="width: 240px; height: 258px;" width="240"
                                    height="264"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15"
                        style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0"
                        style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0"
                        style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0"
                        style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0"
                        style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24"
                            height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path>
                        </svg></div>
                    <div class="px-4 lg:px-8 py-10 lg:py-14">
                        <div class="border border-foreground/10 bg-foreground/[0.02]">
                            <div class="p-8 lg:p-10">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                                    <div><a href="https://github.com/eigenpal/docx-editor" target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-2xl lg:text-3xl font-medium hover:underline decoration-eigenpal-green-light underline-offset-4 transition-all">eigenpal/docx-editor</a>
                                        <p class="text-sm text-foreground/60 font-roboto-mono mt-2">MIT License</p>
                                    </div>
                                    <div class="flex items-center gap-3"><span
                                            class="flex items-center gap-1.5 text-sm text-foreground/70"><svg width="14"
                                                height="14" viewBox="0 0 24 24" fill="currentColor"
                                                class="text-yellow-500">
                                                <path
                                                    d="M12 .587l3.668 7.568L24 9.306l-6.064 5.828 1.48 8.279L12 19.446l-7.417 3.967 1.481-8.279L0 9.306l8.332-1.151z">
                                                </path>
                                            </svg>510</span><a href="https://www.docx-editor.dev/" target="_blank"
                                            rel="noopener"
                                            class="inline-flex items-center cursor-pointer justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono uppercase tracking-wider border border-foreground/30 bg-transparent text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm gap-2">Project
                                            Page</a><a href="https://github.com/eigenpal/docx-editor" target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center cursor-pointer justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 font-roboto-mono uppercase tracking-wider border border-foreground/30 bg-transparent text-foreground hover:bg-foreground/5 h-10 px-4 py-2 text-sm gap-2"><svg
                                                width="20" height="20" viewBox="0 0 24 24" fill="currentColor"
                                                class="w-4 h-4">
                                                <path
                                                    d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z">
                                                </path>
                                            </svg>View on GitHub</a></div>
                                </div>
                                <p class="text-sm text-foreground/70 leading-relaxed max-w-3xl mb-8">A Google Docs-like
                                    .docx editor for the web. MIT-licensed, client-side, zero server dependencies. Built
                                    for verticals like legal, banking, and insurance where Word fidelity and document
                                    structure are critical.</p>
                                <div class="pt-8 mt-8 border-t border-foreground/10">
                                    <div class="text-xs font-roboto-mono tracking-wide text-foreground/50 mb-4">BUILT
                                        FOR</div>
                                    <div class="flex flex-wrap gap-2"><span
                                            class="px-3 py-1 text-xs border border-foreground/10 text-foreground/70">Legal
                                            &amp; paralegal services</span><span
                                            class="px-3 py-1 text-xs border border-foreground/10 text-foreground/70">Banking
                                            &amp; finance</span><span
                                            class="px-3 py-1 text-xs border border-foreground/10 text-foreground/70">Insurance</span><span
                                            class="px-3 py-1 text-xs border border-foreground/10 text-foreground/70">Document
                                            automation</span><span
                                            class="px-3 py-1 text-xs border border-foreground/10 text-foreground/70">Template
                                            generation</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
