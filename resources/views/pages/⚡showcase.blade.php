<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<main class="flex min-h-screen flex-col pt-[72px]">

    {{-- Hero --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6">
            <div class="relative">
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="left: calc(50% - 50vw);"></div>
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15" style="bottom: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>

                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="pt-20 lg:pt-24 pb-12 lg:pb-16 px-4 lg:px-8 flex items-center justify-between gap-8">
                        <div>
                            <div class="mb-4"><span class="font-roboto-mono text-sm tracking-wide scroll-mt-24 text-foreground/60">/ SHOWCASE</span></div>
                            <h1 class="text-3xl font-medium leading-tight tracking-tight text-foreground md:text-4xl lg:text-5xl mb-4">
                                See Wera<br>in action
                            </h1>
                            <p class="text-base text-foreground/60 max-w-2xl">
                                Explore real workflows, feature walkthroughs, and team use cases — no sign-up required.
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

                {{-- Feature: Kanban Boards --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="grid lg:grid-cols-[3fr_2fr] gap-8 lg:gap-12 items-center px-4 lg:px-8 py-12 lg:py-20">
                        {{-- Kanban board mock --}}
                        <div class="order-last lg:order-first">
                            <div class="border border-foreground/10 shadow-sm overflow-hidden rounded-2xl bg-foreground/[0.02] p-4">
                                <div class="grid grid-cols-4 gap-3">
                                    @php
                                        $boards = [
                                            ['name' => 'Pending', 'color' => '#ef4444', 'tasks' => ['Design landing page', 'Write onboarding copy', 'Set up analytics']],
                                            ['name' => 'Ongoing', 'color' => '#3b82f6', 'tasks' => ['Build auth flow', 'Mobile nav component']],
                                            ['name' => 'Review', 'color' => '#eab308', 'tasks' => ['Dashboard layout', 'API docs']],
                                            ['name' => 'Completed', 'color' => '#22c55e', 'tasks' => ['Project setup', 'Database schema', 'CI pipeline']],
                                        ];
                                    @endphp
                                    @foreach($boards as $board)
                                    <div>
                                        <div class="flex items-center gap-2 mb-2 px-1">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $board['color'] }}"></span>
                                            <span class="text-xs font-roboto-mono tracking-wide text-foreground/60 uppercase">{{ $board['name'] }}</span>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($board['tasks'] as $task)
                                            <div class="bg-background border border-foreground/8 rounded p-2.5 text-xs text-foreground/80 leading-snug">{{ $task }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-roboto-mono text-sm tracking-wide text-foreground/60 mb-4">/ BOARDS</span>
                            <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mb-6">
                                Visual boards that match your workflow
                            </h2>
                            <div class="space-y-4 text-sm text-foreground/70 leading-relaxed">
                                <p>Each project gets its own Kanban board with customizable columns. Move tasks between Pending, Ongoing, Review, and Completed — or rename them to match your process.</p>
                                <p>Tasks are sorted by position and can be reordered within any column by dragging. Every change is reflected in real time across all team members.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>

                {{-- Feature: Task detail --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="grid lg:grid-cols-[2fr_3fr] gap-8 lg:gap-12 items-center px-4 lg:px-8 py-12 lg:py-20">
                        <div class="flex flex-col">
                            <span class="font-roboto-mono text-sm tracking-wide text-foreground/60 mb-4">/ TASKS</span>
                            <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mb-6">
                                Every task tells<br>the full story
                            </h2>
                            <div class="space-y-4 mb-8">
                                <div class="flex items-start gap-3">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-0.5"><path d="M20 6L9 17L4 12" stroke="#A99F8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div>
                                        <div class="text-sm font-medium mb-0.5">Rich descriptions</div>
                                        <div class="text-sm text-foreground/60">Markdown-supported descriptions with code blocks, links, and formatting.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-0.5"><path d="M20 6L9 17L4 12" stroke="#A99F8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div>
                                        <div class="text-sm font-medium mb-0.5">Checklists with progress</div>
                                        <div class="text-sm text-foreground/60">Break any task into sub-items. Completion progress is tracked automatically.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-0.5"><path d="M20 6L9 17L4 12" stroke="#A99F8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div>
                                        <div class="text-sm font-medium mb-0.5">Assignees, due dates, priority</div>
                                        <div class="text-sm text-foreground/60">Assign multiple team members, set deadlines, and mark urgency level.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-0.5"><path d="M20 6L9 17L4 12" stroke="#A99F8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div>
                                        <div class="text-sm font-medium mb-0.5">File attachments</div>
                                        <div class="text-sm text-foreground/60">Attach screenshots, specs, or any file directly to the task.</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="flex-shrink-0 mt-0.5"><path d="M20 6L9 17L4 12" stroke="#A99F8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div>
                                        <div class="text-sm font-medium mb-0.5">Comments & mentions</div>
                                        <div class="text-sm text-foreground/60">Discuss right on the task. Mention teammates to notify them instantly.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Task mock --}}
                        <div class="border border-foreground/10 rounded-2xl overflow-hidden bg-foreground/[0.02]">
                            <div class="border-b border-foreground/10 px-6 py-4 flex items-center justify-between">
                                <span class="text-xs font-roboto-mono tracking-wide text-foreground/50 uppercase">Task detail</span>
                                <span class="px-2 py-0.5 text-xs border border-red-500/30 text-red-400 font-roboto-mono">HIGH</span>
                            </div>
                            <div class="p-6">
                                <h3 class="font-medium mb-1">Implement payment flow with Stripe</h3>
                                <p class="text-sm text-foreground/60 mb-4">Set up Stripe Elements for card input, handle webhooks for payment confirmation, and send email receipts on success.</p>
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-foreground/50">Checklist</span>
                                        <span class="text-xs text-foreground/50">2 / 4</span>
                                    </div>
                                    <div class="w-full bg-foreground/10 rounded-full h-1 mb-3">
                                        <div class="bg-eigenpal-green-light h-1 rounded-full" style="width: 50%"></div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-2 text-sm"><span class="w-4 h-4 border border-eigenpal-green-light flex items-center justify-center flex-shrink-0"><svg width="10" height="10" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#86efac" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="text-foreground/50 line-through">Set up Stripe account & API keys</span></div>
                                        <div class="flex items-center gap-2 text-sm"><span class="w-4 h-4 border border-eigenpal-green-light flex items-center justify-center flex-shrink-0"><svg width="10" height="10" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#86efac" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="text-foreground/50 line-through">Build checkout UI component</span></div>
                                        <div class="flex items-center gap-2 text-sm"><span class="w-4 h-4 border border-foreground/20 flex-shrink-0"></span><span class="text-foreground/80">Handle webhook events</span></div>
                                        <div class="flex items-center gap-2 text-sm"><span class="w-4 h-4 border border-foreground/20 flex-shrink-0"></span><span class="text-foreground/80">Implement email receipts</span></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 pt-4 border-t border-foreground/10">
                                    <div class="flex -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-foreground/20 border border-background flex items-center justify-center text-xs">A</div>
                                        <div class="w-6 h-6 rounded-full bg-foreground/30 border border-background flex items-center justify-center text-xs">M</div>
                                    </div>
                                    <span class="text-xs text-foreground/50">Due March 28</span>
                                    <span class="text-xs text-foreground/50 ml-auto">3 comments</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>

                {{-- Feature: Guest ticket --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="grid lg:grid-cols-[3fr_2fr] gap-8 lg:gap-12 items-center px-4 lg:px-8 py-12 lg:py-20 bg-foreground/[0.02]">
                        {{-- Ticket chat mock --}}
                        <div class="order-last lg:order-first">
                            <div class="border border-foreground/10 rounded-2xl overflow-hidden">
                                <div class="border-b border-foreground/10 px-6 py-3">
                                    <span class="text-xs font-roboto-mono tracking-wide text-foreground/50 uppercase">Submit a ticket — Acme Corp · Support</span>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="flex justify-start">
                                        <div class="bg-foreground/5 rounded-xl rounded-tl-none px-4 py-3 text-sm max-w-xs">
                                            Hi! I can help you create a ticket. What is the ticket title?
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <div class="bg-eigenpal-black text-white rounded-xl rounded-tr-none px-4 py-3 text-sm max-w-xs">
                                            Login page shows blank screen after password reset
                                        </div>
                                    </div>
                                    <div class="flex justify-start">
                                        <div class="bg-foreground/5 rounded-xl rounded-tl-none px-4 py-3 text-sm max-w-xs">
                                            Great. Please describe the issue in detail.
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <div class="bg-eigenpal-black text-white rounded-xl rounded-tr-none px-4 py-3 text-sm max-w-xs">
                                            After resetting my password, clicking the link sends me back to the login page but it's completely white. Happens in Chrome and Firefox.
                                        </div>
                                    </div>
                                    <div class="flex justify-start">
                                        <div class="bg-foreground/5 rounded-xl rounded-tl-none px-4 py-3 text-sm max-w-xs">
                                            Thanks! What's your name?
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t border-foreground/10 px-4 py-3 flex gap-2">
                                    <input type="text" placeholder="Type a message..." class="flex-1 bg-transparent text-sm outline-none text-foreground/50 placeholder:text-foreground/30" disabled>
                                    <button class="px-3 py-1.5 text-xs bg-foreground/10 text-foreground/60 font-roboto-mono" disabled>Send</button>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-roboto-mono text-sm tracking-wide text-foreground/60 mb-4">/ GUEST TICKETS</span>
                            <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mb-6">
                                Let clients submit tickets without an account
                            </h2>
                            <div class="space-y-4 text-sm text-foreground/70 leading-relaxed">
                                <p>Every project gets an embeddable ticket widget. Share a link or embed it in your product — and clients can submit issues through a guided chat interface, no login needed.</p>
                                <p>Submitted tickets land directly on your board as tasks, complete with title, description, and submitter details ready for triage.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>

                {{-- Feature stats --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y divide-foreground/10">
                        @php
                            $stats = [
                                ['value' => '< 5 min', 'label' => 'Time to first board'],
                                ['value' => '100%', 'label' => 'Real-time sync'],
                                ['value' => 'Native', 'label' => 'iOS & Android apps'],
                                ['value' => '0', 'label' => 'Required training'],
                            ];
                        @endphp
                        @foreach($stats as $stat)
                        <div class="px-8 py-10">
                            <div class="text-3xl lg:text-4xl font-medium mb-2">{{ $stat['value'] }}</div>
                            <div class="text-sm text-foreground/60">{{ $stat['label'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6 py-16 lg:py-24">
            <div class="text-center max-w-xl mx-auto">
                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ GET STARTED</span>
                <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4 mb-6">
                    Ready to try it with your team?
                </h2>
                <p class="text-base text-foreground/60 mb-8">
                    Set up a workspace, create your first project, and start moving work in minutes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider text-sm bg-eigenpal-black hover:bg-eigenpal-black/90 h-12 px-6 py-3 btn-gradient group text-black">
                        Get in touch
                        <span class="flex items-center justify-center w-8 h-8 bg-[#9177CF]">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-white">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                    <a href="{{ route('why') }}"
                        class="inline-flex items-center cursor-pointer justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider border border-foreground/30 bg-transparent text-foreground hover:bg-foreground/5 h-12 px-6 py-3 text-sm">
                        Why Wera?
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>