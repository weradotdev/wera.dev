<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Status Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 28px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 14px; margin-bottom: 20px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left { width: 68%; vertical-align: top; }
        .header-right { width: 32%; text-align: right; vertical-align: top; }
        .title { font-size: 24px; font-weight: 700; margin: 0 0 4px 0; color: #0f172a; }
        .subtitle { font-size: 13px; margin: 0; color: #475569; }
        .meta { margin-top: 8px; font-size: 11px; color: #64748b; }
        .logos { margin-top: 8px; }
        .logo-box { display: inline-block; width: 86px; height: 86px; border: 1px solid #e2e8f0; border-radius: 8px; margin-left: 8px; text-align: center; vertical-align: top; }
        .logo-box img { width: 84px; height: 84px; object-fit: contain; border-radius: 8px; }
        .logo-fallback { line-height: 86px; font-size: 10px; color: #94a3b8; }

        .section-title { font-size: 14px; font-weight: 700; margin: 18px 0 8px; color: #0f172a; }
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .summary-table th, .summary-table td { border: 1px solid #e2e8f0; padding: 8px 10px; }
        .summary-table th { background: #f8fafc; text-align: left; }

        .status-grid { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .status-grid td { width: 20%; border: 1px solid #e2e8f0; padding: 8px 10px; }
        .status-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }
        .status-value { font-size: 18px; font-weight: 700; margin-top: 2px; color: #0f172a; }

        .task-group { margin-top: 14px; }
        .task-group-title { font-size: 12px; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
        .task-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .task-table th, .task-table td { border: 1px solid #e2e8f0; padding: 7px 8px; vertical-align: top; }
        .task-table th { background: #f8fafc; text-align: left; font-size: 11px; }
        .task-table td { font-size: 11px; }
        .comment { color: #334155; }
        .muted { color: #64748b; }

        .footer { margin-top: 18px; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <h1 class="title">Project Status Report</h1>
                    <p class="subtitle">{{ $project->name }}</p>
                    <div class="meta">
                        Workspace: {{ $project->workspace?->name ?? 'N/A' }}<br>
                        Project Status: {{ \Illuminate\Support\Str::title((string) $project->status) }}<br>
                        Generated: {{ $generatedAt->format('M d, Y H:i') }}
                    </div>
                </td>
                <td class="header-right">
                    <div class="logos">
                        <div class="logo-box">
                            @if($workspaceLogo ?? null)
                                <img src="{{ $workspaceLogo }}" alt="Workspace Logo">
                            @else
                                <div class="logo-fallback">Workspace</div>
                            @endif
                        </div>
                        <div class="logo-box">
                            @if($projectLogo)
                                <img src="{{ $projectLogo }}" alt="Project Logo">
                            @else
                                <div class="logo-fallback">Project</div>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">Executive Summary</h2>
    <table class="status-grid">
        <tr>
            <td>
                <div class="status-label">Pending</div>
                <div class="status-value">{{ $reportData['status_summary']['pending'] ?? 0 }}</div>
            </td>
            <td>
                <div class="status-label">Ongoing</div>
                <div class="status-value">{{ $reportData['status_summary']['ongoing'] ?? 0 }}</div>
            </td>
            <td>
                <div class="status-label">Review</div>
                <div class="status-value">{{ $reportData['status_summary']['review'] ?? 0 }}</div>
            </td>
            <td>
                <div class="status-label">Completed</div>
                <div class="status-value">{{ $reportData['status_summary']['completed'] ?? 0 }}</div>
            </td>
            <td>
                <div class="status-label">Total Tasks</div>
                <div class="status-value">{{ $reportData['total_tasks'] ?? 0 }}</div>
            </td>
        </tr>
    </table>

    <h2 class="section-title">Latest Project Comment</h2>
    @php($projectComment = $reportData['project_last_comment'] ?? null)
    @if($projectComment)
        <table class="summary-table">
            <tr>
                <th style="width: 20%;">Author</th>
                <th style="width: 20%;">Date</th>
                <th>Comment</th>
            </tr>
            <tr>
                <td>{{ $projectComment['author'] ?? 'Unknown' }}</td>
                <td>{{ optional($projectComment['created_at'])->format('M d, Y H:i') }}</td>
                <td class="comment">{{ $projectComment['body'] ?? 'N/A' }}</td>
            </tr>
        </table>
    @else
        <p class="muted">No project comments available.</p>
    @endif

    <h2 class="section-title">Task Details</h2>
    @php($labels = ['pending' => 'Pending', 'ongoing' => 'Ongoing', 'review' => 'Review', 'completed' => 'Completed', 'other' => 'Other'])
    @foreach($labels as $key => $label)
        @php($items = $reportData['categorized_tasks'][$key] ?? collect())
        @if($items->isNotEmpty())
            <div class="task-group">
                <p class="task-group-title">{{ $label }} ({{ $items->count() }})</p>
                <table class="task-table">
                    <tr>
                        <th style="width: 20%;">Task</th>
                        <th style="width: 14%;">Board</th>
                        <th style="width: 10%;">Priority</th>
                        <th style="width: 14%;">Assignee</th>
                        <th style="width: 16%;">Schedule</th>
                        <th>Latest Comment</th>
                    </tr>
                    @foreach($items as $task)
                        <tr>
                            <td>{{ $task['title'] }}</td>
                            <td>{{ $task['status'] }}</td>
                            <td>{{ $task['priority'] }}</td>
                            <td>{{ $task['assignee'] ?? 'Unassigned' }}</td>
                            <td>
                                {{ optional($task['start_at'])->format('M d, Y H:i') }}
                                @if($task['end_at'])
                                    <br>to {{ optional($task['end_at'])->format('M d, Y H:i') }}
                                @endif
                            </td>
                            <td class="comment">
                                @if($task['last_comment'])
                                    <strong>{{ $task['last_comment']['author'] ?? 'Unknown' }}</strong>
                                    <span class="muted">({{ optional($task['last_comment']['created_at'])->format('M d, Y H:i') }})</span><br>
                                    {{ $task['last_comment']['body'] ?? '' }}
                                @else
                                    <span class="muted">No comments</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    @endforeach

    <div class="footer">
        Generated by {{ config('app.name') }} • Confidential Project Report
    </div>
</body>
</html>
