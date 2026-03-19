<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Executive Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 28px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 14px; margin-bottom: 18px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left { width: 70%; vertical-align: top; }
        .header-right { width: 30%; text-align: right; vertical-align: top; }
        .title { font-size: 24px; margin: 0; font-weight: 700; color: #0f172a; }
        .subtitle { margin: 4px 0 0; color: #475569; font-size: 13px; }
        .meta { margin-top: 8px; color: #64748b; font-size: 11px; }

        .logos { margin-top: 8px; }
        .logo-box { display: inline-block; width: 86px; height: 86px; border: 1px solid #e2e8f0; border-radius: 8px; margin-left: 8px; text-align: center; }
        .logo-box img { width: 84px; height: 84px; object-fit: contain; border-radius: 8px; }
        .logo-fallback { line-height: 86px; color: #94a3b8; font-size: 10px; }

        .section-title { font-size: 14px; margin: 16px 0 8px; font-weight: 700; color: #0f172a; }
        .kpi-table { width: 100%; border-collapse: collapse; }
        .kpi-table td { width: 20%; border: 1px solid #e2e8f0; padding: 8px 10px; }
        .kpi-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
        .kpi-value { margin-top: 2px; font-size: 18px; font-weight: 700; color: #0f172a; }

        .summary-table, .milestone-table { width: 100%; border-collapse: collapse; }
        .summary-table th, .summary-table td,
        .milestone-table th, .milestone-table td { border: 1px solid #e2e8f0; padding: 8px 10px; vertical-align: top; }
        .summary-table th, .milestone-table th { background: #f8fafc; text-align: left; font-size: 11px; }
        .muted { color: #64748b; }

        .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <h1 class="title">Project Executive Brief</h1>
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

    <h2 class="section-title">Executive KPIs</h2>
    <table class="kpi-table">
        <tr>
            <td><div class="kpi-label">Total Tasks</div><div class="kpi-value">{{ $reportData['task_total'] }}</div></td>
            <td><div class="kpi-label">Completion Rate</div><div class="kpi-value">{{ $reportData['completion_rate'] }}%</div></td>
            <td><div class="kpi-label">Ongoing</div><div class="kpi-value">{{ $reportData['status_totals']['ongoing'] ?? 0 }}</div></td>
            <td><div class="kpi-label">Overdue</div><div class="kpi-value">{{ $reportData['overdue_count'] }}</div></td>
            <td><div class="kpi-label">High Risk</div><div class="kpi-value">{{ $reportData['high_risk_count'] }}</div></td>
        </tr>
    </table>

    <h2 class="section-title">Task Status Mix</h2>
    <table class="summary-table">
        <tr>
            <th style="width: 20%;">Pending</th>
            <th style="width: 20%;">Ongoing</th>
            <th style="width: 20%;">Review</th>
            <th style="width: 20%;">Completed</th>
            <th style="width: 20%;">Other</th>
        </tr>
        <tr>
            <td>{{ $reportData['status_totals']['pending'] ?? 0 }}</td>
            <td>{{ $reportData['status_totals']['ongoing'] ?? 0 }}</td>
            <td>{{ $reportData['status_totals']['review'] ?? 0 }}</td>
            <td>{{ $reportData['status_totals']['completed'] ?? 0 }}</td>
            <td>{{ $reportData['status_totals']['other'] ?? 0 }}</td>
        </tr>
    </table>

    <h2 class="section-title">Upcoming Milestones</h2>
    @if($reportData['upcoming_milestones']->isEmpty())
        <p class="muted">No upcoming milestones available.</p>
    @else
        <table class="milestone-table">
            <tr>
                <th style="width: 36%;">Task</th>
                <th style="width: 16%;">Priority</th>
                <th style="width: 24%;">Assignee</th>
                <th>Target Date</th>
            </tr>
            @foreach($reportData['upcoming_milestones'] as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ \Illuminate\Support\Str::title((string) $task->priority) }}</td>
                    <td>{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ optional($task->end_at)->format('M d, Y H:i') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <h2 class="section-title">Latest Project Comment</h2>
    @php($projectComment = $reportData['project_last_comment'] ?? null)
    @if($projectComment)
        <table class="summary-table">
            <tr>
                <th style="width: 22%;">Author</th>
                <th style="width: 22%;">Date</th>
                <th>Comment</th>
            </tr>
            <tr>
                <td>{{ $projectComment['author'] ?? 'Unknown' }}</td>
                <td>{{ optional($projectComment['created_at'])->format('M d, Y H:i') }}</td>
                <td>{{ $projectComment['body'] ?? '' }}</td>
            </tr>
        </table>
    @else
        <p class="muted">No project comments available.</p>
    @endif

    <div class="footer">
        Generated by {{ config('app.name') }} • Executive Report
    </div>
</body>
</html>
