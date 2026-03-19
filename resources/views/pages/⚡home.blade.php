<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<main class="flex min-h-screen flex-col pt-18">

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

                    <div class="px-4 lg:px-8 pt-20 lg:pt-28 pb-16 lg:pb-24">
                        <div class="max-w-3xl">
                            <div class="mb-6">
                                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ PROJECT MANAGEMENT</span>
                            </div>
                            <h1 class="text-4xl font-medium leading-tight tracking-tight text-foreground md:text-5xl lg:text-6xl mb-6">
                                The workspace<br>your team<br>actually uses
                            </h1>
                            <p class="text-base lg:text-lg text-foreground/60 max-w-xl mb-10">
                                Wera gives your team shared boards, tasks with full context, and a guest ticket system — with AI that helps without getting in the way.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('showcase') }}" wire:navigate
                                    class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider text-sm bg-eigenpal-black hover:bg-eigenpal-black/90 h-12 px-6 py-3 btn-gradient group text-black">
                                    See it in action
                                    <span class="flex items-center justify-center w-8 h-8 bg-[#9177CF]">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                    </span>
                                </a>
                                <a href="{{ route('why') }}" wire:navigate
                                    class="inline-flex items-center cursor-pointer justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider border border-foreground/30 bg-transparent text-foreground hover:bg-foreground/5 h-12 px-6 py-3 text-sm">
                                    Why Wera?
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Board preview --}}
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="px-4 lg:px-8 py-10 lg:py-14 overflow-x-auto">
                        <div class="min-w-[640px] bg-foreground/[0.02] border border-foreground/10 rounded-2xl p-5">
                            {{-- Board header --}}
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-md bg-foreground/10 flex items-center justify-center text-xs font-medium">W</div>
                                    <span class="text-sm font-medium">Website Redesign</span>
                                </div>
                                <div class="flex -space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-foreground/20 border-2 border-background flex items-center justify-center text-xs">A</div>
                                    <div class="w-7 h-7 rounded-full bg-foreground/30 border-2 border-background flex items-center justify-center text-xs">M</div>
                                    <div class="w-7 h-7 rounded-full bg-foreground/15 border-2 border-background flex items-center justify-center text-xs">S</div>
                                </div>
                            </div>
                            {{-- Columns --}}
                            <div class="grid grid-cols-4 gap-3">
                                @php
                                    $boards = [
                                        ['name' => 'Pending', 'color' => '#ef4444', 'tasks' => [
                                            ['title' => 'Homepage hero copy', 'priority' => 'high'],
                                            ['title' => 'Set up analytics', 'priority' => 'low'],
                                            ['title' => 'Write onboarding docs', 'priority' => 'medium'],
                                        ]],
                                        ['name' => 'Ongoing', 'color' => '#3b82f6', 'tasks' => [
                                            ['title' => 'Auth flow redesign', 'priority' => 'high'],
                                            ['title' => 'Mobile nav component', 'priority' => 'medium'],
                                        ]],
                                        ['name' => 'Review', 'color' => '#eab308', 'tasks' => [
                                            ['title' => 'Dashboard layout', 'priority' => 'medium'],
                                            ['title' => 'API documentation', 'priority' => 'low'],
                                        ]],
                                        ['name' => 'Done', 'color' => '#22c55e', 'tasks' => [
                                            ['title' => 'Project setup', 'priority' => 'low'],
                                            ['title' => 'CI/CD pipeline', 'priority' => 'high'],
                                            ['title' => 'Database schema', 'priority' => 'medium'],
                                        ]],
                                    ];
                                    $priorityColors = ['high' => '#ef4444', 'medium' => '#eab308', 'low' => '#6b7280'];
                                @endphp
                                @foreach($boards as $board)
                                <div>
                                    <div class="flex items-center gap-1.5 mb-2.5 px-0.5">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $board['color'] }}"></span>
                                        <span class="text-xs font-roboto-mono tracking-wide text-foreground/60 uppercase">{{ $board['name'] }}</span>
                                        <span class="ml-auto text-xs text-foreground/30">{{ count($board['tasks']) }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($board['tasks'] as $task)
                                        <div class="bg-background border border-foreground/10 rounded-lg p-3">
                                            <div class="text-xs text-foreground/80 leading-snug mb-2">{{ $task['title'] }}</div>
                                            <div class="flex items-center justify-between">
                                                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $priorityColors[$task['priority']] }}"></span>
                                                <div class="w-4 h-4 rounded-full bg-foreground/15 text-foreground/40 flex items-center justify-center text-[9px]">+</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6 py-16 lg:py-24">
            <div class="mb-12">
                <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ FEATURES</span>
                <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4">Everything your team needs</h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-px bg-foreground/10">

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="3" width="9" height="18" rx="1" stroke="#A99F8E" stroke-width="1.5"/>
                            <rect x="13" y="3" width="9" height="11" rx="1" stroke="#A99F8E" stroke-width="1.5"/>
                            <rect x="13" y="16" width="9" height="5" rx="1" stroke="#A99F8E" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Kanban boards</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Each project gets customisable columns. Drag tasks between Pending, Ongoing, Review, and Done — or rename them to match your flow.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="9" y="3" width="6" height="4" rx="1" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M9 12L11 14L15 10" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Tasks with full context</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Descriptions, checklists with progress tracking, assignees, due dates, priority, file attachments, and threaded comments — all on the task.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="9" cy="7" r="4" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M23 21V19C22.9986 17.1771 21.765 15.5857 20 15.13" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M16 3.13C17.7699 3.58317 19.0078 5.17799 19.0078 7.005C19.0078 8.832 17.7699 10.4268 16 10.88" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Team workspaces</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Invite teammates to a shared workspace. Everyone in your organisation shares the same source of truth across all projects.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M21 15C21 15.5304 20.7893 16.0391 20.4142 16.4142C20.0391 16.7893 19.5304 17 19 17H7L3 21V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V15Z" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Guest ticket submission</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Let clients submit issues without an account. Embed the ticket widget in your product and incoming tickets land straight on your board.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">AI assistance</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">AI helps draft task descriptions, suggest next steps, and surface blockers — without replacing your team's judgment or decision-making.</p>
                </div>

                <div class="bg-background p-8">
                    <div class="mb-4">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="5" y="2" width="14" height="20" rx="2" stroke="#A99F8E" stroke-width="1.5"/>
                            <path d="M12 18H12.01" stroke="#A99F8E" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9 6H15M9 10H15M9 14H12" stroke="#A99F8E" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="font-medium mb-2">Native mobile apps</h3>
                    <p class="text-sm text-foreground/60 leading-relaxed">Full-featured iOS and Android apps built with Expo. Review tasks, add comments, and stay on top of your projects from anywhere.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- How it works --}}
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
                        <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ HOW IT WORKS</span>
                        <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4">Up and running in minutes</h2>
                    </div>
                    <div class="grid lg:grid-cols-3 gap-px bg-foreground/10">
                        @php
                            $steps = [
                                ['num' => '01', 'title' => 'Create a workspace', 'desc' => 'Sign up and create your team workspace. Invite colleagues by email — they join instantly, no lengthy setup required.'],
                                ['num' => '02', 'title' => 'Add your projects', 'desc' => 'Create a project for each workstream. Each one gets its own Kanban board with Pending, Ongoing, Review, and Done columns out of the box.'],
                                ['num' => '03', 'title' => 'Start moving work', 'desc' => 'Create tasks, assign them, add checklists and files, leave comments. Drag cards across columns as work progresses.'],
                            ];
                        @endphp
                        @foreach($steps as $step)
                        <div class="bg-foreground/[0.02] p-8">
                            <div class="font-roboto-mono text-4xl font-medium text-foreground/10 mb-6">{{ $step['num'] }}</div>
                            <h3 class="font-medium mb-3">{{ $step['title'] }}</h3>
                            <p class="text-sm text-foreground/60 leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6">
            <div class="relative">
                <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-y divide-foreground/10">
                    @php
                        $stats = [
                            ['value' => '< 5 min', 'label' => 'Time to first board'],
                            ['value' => '4', 'label' => 'Default columns per project'],
                            ['value' => '100%', 'label' => 'Real-time sync'],
                            ['value' => 'iOS & Android', 'label' => 'Native mobile apps'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                    <div class="px-8 py-12">
                        <div class="text-3xl lg:text-4xl font-medium mb-2">{{ $stat['value'] }}</div>
                        <div class="text-sm text-foreground/60">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative w-full">
        <div class="mx-auto container px-6 py-16 lg:py-24">
            <div class="relative">
                <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                <div class="px-4 lg:px-8 py-16 lg:py-20 text-center">
                    <span class="font-roboto-mono text-sm tracking-wide text-foreground/60">/ GET STARTED</span>
                    <h2 class="text-2xl lg:text-3xl font-medium leading-tight tracking-tight text-foreground mt-4 mb-4">
                        Your team deserves<br>a better board
                    </h2>
                    <p class="text-base text-foreground/60 max-w-lg mx-auto mb-8">
                        No lengthy onboarding. No sales call required. Start a workspace, create a project, and see your team's work in one place.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('contact') }}" wire:navigate
                            class="inline-flex items-center cursor-pointer justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider text-sm bg-eigenpal-black hover:bg-eigenpal-black/90 h-12 px-6 py-3 btn-gradient group text-black">
                            Get in touch
                            <span class="flex items-center justify-center w-8 h-8 bg-[#9177CF]">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-white"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </span>
                        </a>
                        <a href="{{ route('download') }}" wire:navigate
                            class="inline-flex items-center cursor-pointer justify-center whitespace-nowrap font-medium transition-colors focus-visible:outline-none font-roboto-mono uppercase tracking-wider border border-foreground/30 bg-transparent text-foreground hover:bg-foreground/5 h-12 px-6 py-3 text-sm">
                            Download the app
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

