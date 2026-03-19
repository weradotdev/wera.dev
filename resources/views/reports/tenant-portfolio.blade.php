<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Portfolio Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 26px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 12px; margin-bottom: 18px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left { width: 68%; vertical-align: top; }
        .header-right { width: 32%; text-align: right; vertical-align: top; }
        .title { font-size: 23px; margin: 0; font-weight: 700; color: #0f172a; }
        .subtitle { margin: 4px 0 0; color: #475569; font-size: 13px; }
        .meta { margin-top: 8px; color: #64748b; font-size: 11px; }

        .logos { margin-top: 8px; }
        .logo-box { display: inline-block; width: 84px; height: 84px; border: 1px solid #e2e8f0; border-radius: 8px; margin-left: 8px; text-align: center; }
        .logo-box img { width: 82px; height: 82px; object-fit: contain; border-radius: 8px; }
        .logo-fallback { line-height: 84px; color: #94a3b8; font-size: 10px; }

        .section-title { font-size: 14px; margin: 16px 0 8px; font-weight: 700; color: #0f172a; }
        .kpi-table, .grid-table, .task-table { width: 100%; border-collapse: collapse; }
        .kpi-table td { border: 1px solid #e2e8f0; width: 20%; padding: 8px 10px; }
        .kpi-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
        .kpi-value { margin-top: 2px; font-size: 17px; font-weight: 700; color: #0f172a; }

        .grid-table th, .grid-table td,
        .task-table th, .task-table td { border: 1px solid #e2e8f0; padding: 7px 8px; vertical-align: top; }
        .grid-table th, .task-table th { background: #f8fafc; text-align: left; font-size: 11px; }
        .grid-table td, .task-table td { font-size: 11px; }
        .muted { color: #64748b; }

        .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 10px; }
        .spacer { height: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <h1 class="title">Tenant Portfolio Report</h1>
                    <p class="subtitle">{{ $tenant->name }}</p>
                    <div class="meta">
                        Total Projects: {{ $portfolioData['project_count'] }}<br>
                        Generated: {{ $generatedAt->format('M d, Y H:i') }}
                    </div>
                </td>
                <td class="header-right">
                    <div class="logos">
                        <div class="logo-box">
                            @if($tenantLogo)
                                <img src="{{ $tenantLogo }}" alt="Tenant Logo">
                            @else
                                <div class="logo-fallback">Tenant</div>
                            @endif
                        </div>
                        <div class="logo-box">
                            @if($projectLogo)
                                <img src="{{ $projectLogo }}" alt="Project Logo">
                            @else
                                <div class="logo-fallback">Portfolio</div>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">Portfolio Summary</h2>
    <table class="kpi-table">
        <tr>
            <td><div class="kpi-label">Projects</div><div class="kpi-value">{{ $portfolioData['project_count'] }}</div></td>
            <td><div class="kpi-label">Total Tasks</div><div class="kpi-value">{{ $portfolioData['task_total'] }}</div></td>
            <td><div class="kpi-label">Overdue Tasks</div><div class="kpi-value">{{ $portfolioData['overdue_tasks']->count() }}</div></td>
            <td><div class="kpi-label">Risk Tasks</div><div class="kpi-value">{{ $portfolioData['risk_tasks']->count() }}</div></td>
            <td><div class="kpi-label">Completed</div><div class="kpi-value">{{ $portfolioData['status_totals']['completed'] ?? 0 }}</div></td>
        </tr>
    </table>

    <h2 class="section-title">Project Breakdown</h2>
    <table class="grid-table">
        <tr>
            <th style="width: 22%;">Project</th>
            <th style="width: 8%;">Status</th>
            <th style="width: 8%;">Tasks</th>
            <th style="width: 7%;">Pending</th>
            <th style="width: 7%;">Ongoing</th>
            <th style="width: 7%;">Review</th>
            <th style="width: 9%;">Completed</th>
            <th>Latest Comment</th>
        </tr>
        @foreach($portfolioData['projects'] as $project)
            <tr>
                <td>{{ $project['name'] }}</td>
                <td>{{ $project['status'] }}</td>
                <td>{{ $project['task_count'] }}</td>
                <td>{{ $project['counts']['pending'] }}</td>
                <td>{{ $project['counts']['ongoing'] }}</td>
                <td>{{ $project['counts']['review'] }}</td>
                <td>{{ $project['counts']['completed'] }}</td>
                <td>
                    @if($project['latest_comment'])
                        <strong>{{ $project['latest_comment']['author'] ?? 'Unknown' }}</strong>
                        <span class="muted">({{ optional($project['latest_comment']['created_at'])->format('M d, Y H:i') }})</span><br>
                        {{ $project['latest_comment']['body'] ?? '' }}
                    @else
                        <span class="muted">No comments</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

    <div class="spacer"></div>

    <h2 class="section-title">Overdue Tasks</h2>
    @if($portfolioData['overdue_tasks']->isEmpty())
        <p class="muted">No overdue tasks.</p>
    @else
        <table class="task-table">
            <tr>
                <th style="width: 24%;">Project</th>
                <th style="width: 28%;">Task</th>
                <th style="width: 20%;">Assignee</th>
                <th>Due</th>
            </tr>
            @foreach($portfolioData['overdue_tasks'] as $task)
                <tr>
                    <td>{{ $task['project'] }}</td>
                    <td>{{ $task['task'] }}</td>
                    <td>{{ $task['assignee'] ?? 'Unassigned' }}</td>
                    <td>{{ optional($task['end_at'])->format('M d, Y H:i') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <h2 class="section-title">Risk Watchlist</h2>
    @if($portfolioData['risk_tasks']->isEmpty())
        <p class="muted">No risk tasks currently flagged.</p>
    @else
        <table class="task-table">
            <tr>
                <th style="width: 18%;">Project</th>
                <th style="width: 24%;">Task</th>
                <th style="width: 10%;">Priority</th>
                <th style="width: 16%;">Status</th>
                <th style="width: 16%;">Assignee</th>
                <th>Due</th>
            </tr>
            @foreach($portfolioData['risk_tasks'] as $task)
                <tr>
                    <td>{{ $task['project'] }}</td>
                    <td>{{ $task['task'] }}</td>
                    <td>{{ $task['priority'] }}</td>
                    <td>{{ $task['status'] }}</td>
                    <td>{{ $task['assignee'] ?? 'Unassigned' }}</td>
                    <td>{{ optional($task['end_at'])->format('M d, Y H:i') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="footer">
        Generated by {{ config('app.name') }} • Confidential Portfolio Report
    </div>
</body>
</html>
