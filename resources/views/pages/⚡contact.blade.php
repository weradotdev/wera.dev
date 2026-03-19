<?php

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $company = '';
    public string $subject = '';
    public string $message = '';
    public bool $submitted = false;
    public ?string $error = null;

    public function send(): void
    {
        $validated = $this->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:200'],
            'company' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        try {
            Mail::raw(
                "Name: {$validated['name']}\nEmail: {$validated['email']}\nCompany: {$validated['company']}\nSubject: {$validated['subject']}\n\n{$validated['message']}",
                function ($mail) use ($validated) {
                    $mail->to(config('mail.from.address'))
                         ->subject('[Wera Contact] ' . $validated['subject'])
                         ->replyTo($validated['email'], $validated['name']);
                }
            );
            $this->submitted = true;
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed', ['error' => $e->getMessage()]);
            $this->error = 'Something went wrong. Please try again or email us directly.';
        }
    }
};
?>

<main class="flex min-h-screen flex-col pt-[72px]">

    <section class="relative w-full">
        <div class="mx-auto container px-6">
            <div class="relative">
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="left: calc(50% - 50vw);"></div>
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15" style="bottom: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>

                {{-- Header --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="pt-20 lg:pt-24 pb-12 lg:pb-16 px-4 lg:px-8 flex items-center justify-between gap-8">
                        <div>
                            <div class="mb-4"><span class="font-roboto-mono text-sm tracking-wide scroll-mt-24 text-foreground/60">/ CONTACT</span></div>
                            <h1 class="text-3xl font-medium leading-tight tracking-tight text-foreground md:text-4xl lg:text-5xl mb-4">
                                Let's talk
                            </h1>
                            <p class="text-base text-foreground/60 max-w-2xl">
                                Have a question, want a demo, or just want to say hi? We read every message.
                            </p>
                        </div>
                        <div class="hidden lg:block flex-shrink-0" style="width: 240px; height: 258px;">
                            <div class="pointer-events-none flex items-center justify-center"><canvas class="pointer-events-none" style="width: 240px; height: 258px;" width="240" height="264"></canvas></div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>

                {{-- Content --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="grid lg:grid-cols-[2fr_3fr] gap-0 divide-y lg:divide-y-0 lg:divide-x divide-foreground/10">

                        {{-- Left: Info --}}
                        <div class="px-4 lg:px-8 py-10 lg:py-14">
                            <div class="space-y-8">
                                <div>
                                    <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-3">Email</div>
                                    <a href="mailto:hello@wera.dev" class="text-foreground hover:underline decoration-eigenpal-green-light underline-offset-4">hello@wera.dev</a>
                                </div>
                                <div>
                                    <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-3">Response time</div>
                                    <p class="text-sm text-foreground/70">We typically reply within 1 business day.</p>
                                </div>
                                <div>
                                    <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-3">What to include</div>
                                    <ul class="space-y-2 text-sm text-foreground/70">
                                        <li class="flex items-start gap-2"><span class="mt-1.5 w-1 h-1 rounded-full bg-foreground/40 flex-shrink-0"></span>Your team size and industry</li>
                                        <li class="flex items-start gap-2"><span class="mt-1.5 w-1 h-1 rounded-full bg-foreground/40 flex-shrink-0"></span>What you're currently using for project management</li>
                                        <li class="flex items-start gap-2"><span class="mt-1.5 w-1 h-1 rounded-full bg-foreground/40 flex-shrink-0"></span>What's not working about it</li>
                                    </ul>
                                </div>
                                <div class="pt-4 border-t border-foreground/10">
                                    <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-3">Follow along</div>
                                    <div class="flex gap-4">
                                        <a href="https://github.com/weradotdev" target="_blank" rel="noopener noreferrer" class="text-foreground/60 hover:text-foreground transition-colors">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"></path></svg>
                                        </a>
                                        <a href="https://x.com/weradotdev" target="_blank" rel="noopener noreferrer" class="text-foreground/60 hover:text-foreground transition-colors">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.911-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Form --}}
                        <div class="px-4 lg:px-8 py-10 lg:py-14">

                            @if($submitted)
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-12 h-12 border border-eigenpal-green-light flex items-center justify-center mb-6">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#86efac" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <h3 class="text-xl font-medium mb-2">Message sent</h3>
                                    <p class="text-sm text-foreground/60">We'll be in touch within 1 business day.</p>
                                </div>
                            @else
                                <form wire:submit="send" class="space-y-6">

                                    @if($error)
                                        <div class="px-4 py-3 border border-red-500/30 text-red-400 text-sm">{{ $error }}</div>
                                    @endif

                                    <div class="grid sm:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-2" for="contact-name">Name <span class="text-red-400">*</span></label>
                                            <input
                                                id="contact-name"
                                                type="text"
                                                wire:model="name"
                                                autocomplete="name"
                                                placeholder="Your name"
                                                class="w-full border border-foreground/20 bg-transparent px-4 py-3 text-sm text-foreground placeholder:text-foreground/30 focus:outline-none focus:border-foreground/50 transition-colors"
                                            >
                                            @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="block font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-2" for="contact-email">Email <span class="text-red-400">*</span></label>
                                            <input
                                                id="contact-email"
                                                type="email"
                                                wire:model="email"
                                                autocomplete="email"
                                                placeholder="you@company.com"
                                                class="w-full border border-foreground/20 bg-transparent px-4 py-3 text-sm text-foreground placeholder:text-foreground/30 focus:outline-none focus:border-foreground/50 transition-colors"
                                            >
                                            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <div class="grid sm:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-2" for="contact-company">Company</label>
                                            <input
                                                id="contact-company"
                                                type="text"
                                                wire:model="company"
                                                autocomplete="organization"
                                                placeholder="Optional"
                                                class="w-full border border-foreground/20 bg-transparent px-4 py-3 text-sm text-foreground placeholder:text-foreground/30 focus:outline-none focus:border-foreground/50 transition-colors"
                                            >
                                        </div>
                                        <div>
                                            <label class="block font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-2" for="contact-subject">Subject <span class="text-red-400">*</span></label>
                                            <select
                                                id="contact-subject"
                                                wire:model="subject"
                                                class="w-full border border-foreground/20 bg-background px-4 py-3 text-sm text-foreground focus:outline-none focus:border-foreground/50 transition-colors appearance-none"
                                            >
                                                <option value="">Select a topic</option>
                                                <option value="Demo request">Demo request</option>
                                                <option value="Pricing question">Pricing question</option>
                                                <option value="Technical support">Technical support</option>
                                                <option value="Partnership">Partnership</option>
                                                <option value="Feature request">Feature request</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            @error('subject')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-2" for="contact-message">Message <span class="text-red-400">*</span></label>
                                        <textarea
                                            id="contact-message"
                                            wire:model="message"
                                            rows="6"
                                            placeholder="Tell us what's on your mind..."
                                            class="w-full border border-foreground/20 bg-transparent px-4 py-3 text-sm text-foreground placeholder:text-foreground/30 focus:outline-none focus:border-foreground/50 transition-colors resize-none"
                                        ></textarea>
                                        @error('message')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                                    </div>

                                    <div>
                                        <button
                                            type="submit"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider text-sm bg-eigenpal-black hover:bg-eigenpal-black/90 h-12 px-6 py-3 btn-gradient group text-black disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span wire:loading.remove>Send message</span>
                                            <span wire:loading>Sending...</span>
                                            <span wire:loading.remove class="flex items-center justify-center w-8 h-8 bg-[#9177CF]">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-white">
                                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>

                                </form>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>