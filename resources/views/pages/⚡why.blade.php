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
                            <div class="mb-4"><span class="font-roboto-mono text-sm tracking-wide scroll-mt-24 text-foreground/60">/ WHY WERA</span></div>
                            <h1 class="text-3xl font-medium leading-tight tracking-tight text-foreground md:text-4xl lg:text-5xl mb-4">
                                Project management<br>built for how teams<br>actually work
                            </h1>
                            <p class="text-base text-foreground/60 max-w-2xl">
                                Most tools force your team to adapt to the software. Wera adapts to your team — with boards, tasks, and AI that fits into your existing process.
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

                {{-- Problem statement --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="grid lg:grid-cols-2 bg-foreground/[0.04]">
                        <div class="p-8 lg:p-12 border-b lg:border-b-0 lg:border-r border-foreground/10">
                            <div class="mb-8">
                                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ THE PROBLEM</span>
                            </div>
                            <h2 class="text-2xl lg:text-3xl font-medium mb-6">Teams drown in tools, not work</h2>
                            <div class="space-y-5 text-sm text-foreground/70 leading-relaxed">
                                <p>The average knowledge worker switches between 9+ apps daily. Context is lost between Slack threads, email chains, and spreadsheets. Status meetings exist only because no one knows the real status.</p>
                                <p>Jira is powerful but bloated — set-up takes weeks and maintenance takes a dedicated admin. Trello is simple but hits a ceiling the moment a team grows. Notion tries to do everything and ends up doing nothing well.</p>
                                <p>What teams actually need is a focused space: clear boards, tasks with real context, and a way to surface blockers before they become missed deadlines.</p>
                            </div>
                        </div>
                        <div class="p-8 lg:p-12">
                            <div class="mb-8">
                                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ THE ANSWER</span>
                            </div>
                            <h2 class="text-2xl lg:text-3xl font-medium mb-6">One workspace. Every team.</h2>
                            <div class="space-y-5 text-sm text-foreground/70 leading-relaxed">
                                <p>Wera gives every team a shared workspace with projects, boards, and tasks — all in one place. Your developers, designers, and account managers see the same source of truth.</p>
                                <p>Set up takes minutes, not weeks. There's no certification required to create a board. When someone joins, they're productive the same day.</p>
                                <p>And when a task needs more detail, the AI is right there — helping write descriptions, suggest next steps, or surface related items — without leaving the flow.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core principles --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6 py-16 lg:py-24">
            <div class="mb-12">
                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ PRINCIPLES</span>
                <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4">What we believe</h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-foreground/10">

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="2" width="20" height="20" rx="2" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M7 12L10 15L17 8" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Simplicity scales</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">A simple system used consistently is worth more than a perfect system nobody follows. We keep the interface obvious so anyone on the team can navigate it without training.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#A99F8E" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M2 17L12 22L22 17" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12L12 17L22 12" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Context stays with the work</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Every task carries its own history — comments, attachments, checklists. You never have to hunt through Slack to find out why something was decided.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M12 6V12L16 14" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Speed is a feature</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Creating a task should take seconds. Moving work across boards should be instant. We obsess over latency and interaction performance so your workflow never waits on the tool.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="9" cy="7" r="4" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M23 21V19C23 17.1362 21.7252 15.5701 20 15.126" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M16 3.12598C17.7252 3.57006 19 5.13616 19 7.00002C19 8.86388 17.7252 10.4299 16 10.874" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Teams, not individuals</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Work happens in collaboration. Wera is built around shared workspaces and team visibility — not individual productivity dashboards that create silos.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M2 12H22" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M12 2C9.33333 5.33333 8 8.66667 8 12C8 15.3333 9.33333 18.6667 12 22" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M12 2C14.6667 5.33333 16 8.66667 16 12C16 15.3333 14.6667 18.6667 12 22" stroke="#A99F8E" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Open by default</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">We open-source the tools we build while building Wera. If it's useful to us, it's likely useful to someone else. Transparency in tooling builds trust in product.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">AI as an assistant, not a replacement</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">The team decides. The AI helps. We use AI to reduce friction — drafting task descriptions, surfacing blockers, generating summaries — without removing human judgment from the loop.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- Comparison --}}
    <section class="relative w-full bg-foreground/[0.02]">
        <div class="mx-auto container px-6 py-16 lg:py-24">
            <div class="relative">
                <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                <div class="px-4 lg:px-8 py-10 lg:py-14">
                    <div class="mb-10">
                        <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ COMPARISON</span>
                        <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4">How Wera compares</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-foreground/10">
                                    <th class="text-left font-roboto-mono text-xs tracking-wider text-foreground/50 uppercase pb-4 pr-8 w-1/4">Feature</th>
                                    <th class="text-left font-roboto-mono text-xs tracking-wider text-foreground/50 uppercase pb-4 pr-8">Wera</th>
                                    <th class="text-left font-roboto-mono text-xs tracking-wider text-foreground/50 uppercase pb-4 pr-8">Jira</th>
                                    <th class="text-left font-roboto-mono text-xs tracking-wider text-foreground/50 uppercase pb-4 pr-8">Trello</th>
                                    <th class="text-left font-roboto-mono text-xs tracking-wider text-foreground/50 uppercase pb-4">Notion</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-foreground/5">
                                @php
                                    $rows = [
                                        ['Setup time', 'Minutes', 'Days to weeks', 'Minutes', 'Hours'],
                                        ['Kanban boards', '✓ Built-in', '✓ Built-in', '✓ Built-in', '⚠ Manual'],
                                        ['AI assistance', '✓ Native', '⚠ Add-on', '✗ None', '⚠ Limited'],
                                        ['Guest ticket submission', '✓ Embeddable', '✗ None', '✗ None', '✗ None'],
                                        ['Checklist progress', '✓ Auto-tracked', '✗ Manual', '⚠ Plugin', '⚠ Manual'],
                                        ['File attachments', '✓ Per task', '✓ Per task', '⚠ Per card', '✓ Per block'],
                                        ['Custom workflows', '✓ Per project', '✓ Complex', '✗ Limited', '✗ Limited'],
                                        ['Mobile app', '✓ Native iOS & Android', '✓ Available', '✓ Available', '✓ Available'],
                                        ['Open source tools', '✓ Yes', '✗ No', '✗ No', '✗ No'],
                                        ['Price', 'Transparent', 'Per user/month', 'Freemium', 'Per user/month'],
                                    ];
                                @endphp
                                @foreach($rows as $row)
                                <tr>
                                    <td class="py-3 pr-8 text-foreground/60 font-roboto-mono text-xs tracking-wide uppercase">{{ $row[0] }}</td>
                                    <td class="py-3 pr-8 text-foreground font-medium">{{ $row[1] }}</td>
                                    <td class="py-3 pr-8 text-foreground/60">{{ $row[2] }}</td>
                                    <td class="py-3 pr-8 text-foreground/60">{{ $row[3] }}</td>
                                    <td class="py-3 text-foreground/60">{{ $row[4] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6 py-16 lg:py-24">
            <div class="text-center max-w-2xl mx-auto">
                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ GET STARTED</span>
                <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4 mb-6">
                    Ready to see it for yourself?
                </h2>
                <p class="text-base text-foreground/60 mb-8">
                    No lengthy onboarding. No sales call required. Get your workspace up and running in a few minutes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('showcase') }}"
                        class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider text-sm bg-eigenpal-black hover:bg-eigenpal-black/90 h-12 px-6 py-3 btn-gradient group text-black">
                        See the showcase
                        <span class="flex items-center justify-center w-8 h-8 bg-[#9177CF]">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-white">
                                <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                    <a href="{{ route('contact') }}"
                        class="inline-flex items-center cursor-pointer justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider border border-foreground/30 bg-transparent text-foreground hover:bg-foreground/5 h-12 px-6 py-3 text-sm">
                        Talk to us
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>