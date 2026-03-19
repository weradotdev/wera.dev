<?php

use App\Models\Project;
use App\Models\Ticket;
use App\Models\Workspace;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {
    public Workspace $workspace;

    public Project $project;

    /**
     * @var array<int, array{role: 'assistant'|'user'|'system', text: string}>
     */
    public array $messages = [];

    /**
     * @var array{title: string, description: string, name: string, location: string}
     */
    public array $form = [
        'title'       => '',
        'description' => '',
        'name'        => '',
        'location'    => '',
    ];

    public string $draft = '';

    public string $step = 'title';

    public ?int $createdTicketId = null;

    public bool $isSaved = false;

    public function mount(Workspace $workspace, Project $project): void
    {
        abort_unless($project->workspace_id === $workspace->id, 404);

        $this->workspace = $workspace;
        $this->project   = $project;
        $this->messages  = [
            [
                'role' => 'assistant',
                'text' => "Hi! I can help you create a ticket for {$this->project->name}. What is the ticket title?",
            ],
        ];
    }

    public function send(): void
    {
        if ($this->isSaved) {
            return;
        }

        $input = trim($this->draft);
        if ($input === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'text' => $input];

        match ($this->step) {
            'title' => $this->handleTitleStep($input),
            'description' => $this->handleDescriptionStep($input),
            'name' => $this->handleNameStep($input),
            'location' => $this->handleLocationStep($input),
            default => null,
        };

        $this->draft = '';
    }

    protected function handleTitleStep(string $input): void
    {
        if (Str::length($input) < 3) {
            $this->messages[] = [
                'role' => 'assistant',
                'text' => 'Please provide a slightly longer title (at least 3 characters).',
            ];

            return;
        }

        $this->form['title'] = $input;
        $this->step          = 'description';
        $this->messages[]    = [
            'role' => 'assistant',
            'text' => 'Great. Please describe the issue in detail.',
        ];
    }

    protected function handleDescriptionStep(string $input): void
    {
        if (Str::length($input) < 10) {
            $this->messages[] = [
                'role' => 'assistant',
                'text' => 'A bit more detail helps. Please write at least 10 characters.',
            ];

            return;
        }

        $this->form['description'] = $input;
        $this->step                = 'name';
        $this->messages[]          = [
            'role' => 'assistant',
            'text' => 'Thanks. What is your name?',
        ];
    }

    protected function handleNameStep(string $input): void
    {
        $this->form['name'] = $input;
        $this->step         = 'location';
        $this->messages[]   = [
            'role' => 'assistant',
            'text' => 'Got it. What is your location? You can type "skip" if you prefer not to share.',
        ];
    }

    protected function handleLocationStep(string $input): void
    {
        $this->form['location'] = strtolower($input) === 'skip' ? '' : $input;

        $metadata = [
            'Reporter Name' => $this->form['name'] !== '' ? $this->form['name'] : 'Not provided',
            'Location'      => $this->form['location'] !== '' ? $this->form['location'] : 'Not provided',
        ];

        $metadataText = collect($metadata)
            ->map(fn(string $value, string $label): string => "{$label}: {$value}")
            ->implode("\n");

        $description = trim($this->form['description'] . "\n\n---\n" . $metadataText);

        $ticket = Ticket::query()->create([
            'workspace_id' => $this->workspace->id,
            'project_id'   => $this->project->id,
            'title'        => $this->form['title'],
            'description'  => $description,
            'status'       => 'open',
        ]);

        $this->createdTicketId = $ticket->id;
        $this->isSaved         = true;
        $this->step            = 'done';
        $this->messages[]      = [
            'role' => 'system',
            'text' => "Ticket #{$ticket->id} has been created successfully.",
        ];
    }

    public function resetChat(): void
    {
        $this->form            = [
            'title'       => '',
            'description' => '',
            'name'        => '',
            'location'    => '',
        ];
        $this->draft           = '';
        $this->step            = 'title';
        $this->isSaved         = false;
        $this->createdTicketId = null;
        $this->messages        = [
            [
                'role' => 'assistant',
                'text' => "Let's create another ticket for {$this->project->name}. What is the title?",
            ],
        ];
    }
};
?>

<main class="h-screen w-screen bg-slate-950 text-slate-100">
    <style type="text/css">
        header,
        footer {
            visibility: hidden;
            display: none;
        }

    </style>
    <div class="flex h-full w-full flex-col">
        <header class="shrink-0 border-b border-slate-800 bg-slate-900/90 px-4 py-3 backdrop-blur">
            <p class="text-xs uppercase tracking-wider text-cyan-300/80">Support Chat</p>
            <h1 class="text-base font-semibold leading-tight">{{ $workspace->name }} / {{ $project->name }}</h1>
        </header>

        <main class="flex-1 overflow-y-auto px-3 py-4 sm:px-4" id="ticket-chat-scroll">
            <div class="mx-auto flex w-full max-w-2xl flex-col gap-3">
                @foreach ($messages as $message)
                    <div @class([
                        'flex w-full',
                        'justify-end'   => $message['role'] === 'user',
                        'justify-start' => $message['role'] !== 'user',
                    ])>
                        <div @class([
                            'max-w-[90%] rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm',
                            'bg-cyan-500 text-slate-950'                                    => $message['role'] === 'user',
                            'bg-slate-800 text-slate-100'                                   => $message['role'] === 'assistant',
                            'bg-emerald-500/20 text-emerald-200 ring-1 ring-emerald-400/40' => $message['role'] === 'system',
                        ])>
                            {{ $message['text'] }}
                        </div>
                    </div>
                @endforeach

                @if ($isSaved)
                    <div class="rounded-2xl bg-slate-900 p-4 ring-1 ring-slate-700">
                        <p class="text-sm text-slate-300">Saved ticket details</p>
                        <p class="mt-1 text-lg font-semibold">#{{ $createdTicketId }} {{ $form['title'] }}</p>
                        <p class="mt-2 text-sm text-slate-300">{{ $form['description'] }}</p>
                        <button type="button" wire:click="resetChat"
                            class="mt-4 inline-flex items-center rounded-xl bg-cyan-500 px-4 py-2 text-sm font-medium text-slate-950">
                            Create Another Ticket
                        </button>
                    </div>
                @endif
            </div>
        </main>

        <form wire:submit="send" class="shrink-0 border-t border-slate-800 bg-slate-900 p-3 sm:p-4">
            <div class="mx-auto flex w-full max-w-2xl items-end gap-2">
                <div class="flex-1">
                    <label for="ticket-chat-input" class="sr-only">Chat message</label>
                    <textarea id="ticket-chat-input" wire:model="draft" rows="1" @disabled($isSaved)
                        placeholder="{{ $isSaved ? 'Ticket created' : 'Type your response...' }}"
                        class="block w-full resize-none rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-400 focus:border-cyan-400 focus:outline-none"></textarea>
                </div>

                <button type="submit" @disabled($isSaved)
                    class="rounded-full items-center justify-center p-2 bg-[#eee] text-sm font-semibold text-slate-950 disabled:cursor-not-allowed disabled:opacity-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</main>
