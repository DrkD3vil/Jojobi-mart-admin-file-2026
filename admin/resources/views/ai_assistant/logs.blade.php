@extends('layouts.app')

@section('title', 'AI Assistant — Activity Log')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

@php
    $statusMeta = [
        'completed' => ['label' => 'Completed', 'class' => 'ok'],
        'pending_confirmation' => ['label' => 'Awaiting confirm', 'class' => 'warn'],
        'denied' => ['label' => 'Denied', 'class' => 'bad'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'muted'],
        'error' => ['label' => 'Error', 'class' => 'bad'],
    ];
@endphp

<div class="dockline">
    <div class="dl-wrap">
        <div class="dl-head">
            <div>
                <p class="dl-eyebrow">AI Assistant</p>
                <h1>{{ $canViewAll ? 'Activity Log' : 'My Activity' }}</h1>
                <p class="dl-sub">
                    @if($canViewAll)
                        Every question asked and every action taken through the AI Assistant — who did it, which tools ran, and whether it was permitted.
                    @else
                        Your own questions and actions through the AI Assistant. Only admins with RBAC access can see other users' activity.
                    @endif
                </p>
            </div>
            <div class="dl-head-actions">
                <a href="{{ route('ai_assistant.index') }}" class="dl-btn dl-cut">Back to Chat</a>
            </div>
        </div>

        <form method="GET" class="dl-filters">
            <input type="text" name="search" placeholder="Search what was asked…" value="{{ request('search') }}">
            @if($canViewAll)
                <select name="user_id" onchange="this.form.submit()">
                    <option value="">All users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            @endif
            <select name="status" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach($statusMeta as $key => $meta)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
            <button type="submit" class="dl-btn dl-cut">Search</button>
            @if(request('user_id') || request('status') || request('search'))
                <a href="{{ route('ai_assistant.logs') }}" class="dl-btn dl-cut">Clear</a>
            @endif
        </form>

        <div class="dl-board">
            <div class="dl-board-head">
                <span>{{ $logs->total() }} logged {{ Str::plural('turn', $logs->total()) }}</span>
            </div>

            <div class="dl-table-scroll">
                <table class="dl-table">
                    <thead>
                        <tr>
                            <th>When</th>
                            @if($canViewAll)
                                <th>User</th>
                            @endif
                            <th>Request</th>
                            <th>Tools</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $lastDate = null; $colCount = $canViewAll ? 5 : 4; @endphp
                        @forelse($logs as $log)
                            @php $logDate = $log->created_at->toDateString(); @endphp
                            @if($logDate !== $lastDate)
                                <tr class="dl-day-row">
                                    <td colspan="{{ $colCount }}">{{ $log->created_at->isToday() ? 'Today' : ($log->created_at->isYesterday() ? 'Yesterday' : $log->created_at->format('l, F j, Y')) }}</td>
                                </tr>
                                @php $lastDate = $logDate; @endphp
                            @endif
                            @php $meta = $statusMeta[$log->status] ?? ['label' => $log->status, 'class' => 'muted']; @endphp
                            <tr class="dl-row" onclick="document.getElementById('detail-{{ $log->id }}').classList.toggle('open')">
                                <td class="dl-mono">{{ $log->created_at->format('h:i A') }}</td>
                                @if($canViewAll)
                                    <td>{{ $log->user->name ?? 'Deleted user' }}</td>
                                @endif
                                <td class="dl-truncate">{{ Str::limit($log->prompt, 60) }}</td>
                                <td class="dl-mono">
                                    @forelse(($log->tool_calls ?? []) as $call)
                                        <span class="dl-pill">{{ $call['name'] ?? '?' }}</span>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td><span class="dl-status dl-status-{{ $meta['class'] }}">{{ $meta['label'] }}</span></td>
                            </tr>
                            <tr class="dl-detail-row" id="detail-{{ $log->id }}">
                                <td colspan="{{ $colCount }}" class="dl-detail">
                                    <div class="dl-detail-inner">
                                        <div>
                                            <span class="dl-eyebrow">Prompt</span>
                                            <p>{{ $log->prompt }}</p>
                                        </div>
                                        @if($log->response_text)
                                            <div>
                                                <span class="dl-eyebrow">Response</span>
                                                <p>{{ $log->response_text }}</p>
                                            </div>
                                        @endif
                                        @if(!empty($log->tool_results))
                                            <div>
                                                <span class="dl-eyebrow">Tool results</span>
                                                <pre>{{ json_encode($log->tool_results, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                        <div class="dl-detail-meta">
                                            <span>{{ $log->was_write_action ? 'Write action' : 'Read-only' }}</span>
                                            <span>{{ $log->was_allowed ? 'Allowed' : 'Blocked by permissions' }}</span>
                                            <span>IP {{ $log->ip_address ?? '—' }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $colCount }}" class="dl-empty">No activity logged yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->lastPage() > 1)
                <div class="dl-pager">
                    @if($logs->previousPageUrl())
                        <a href="{{ $logs->previousPageUrl() }}" class="dl-btn dl-cut">Prev</a>
                    @endif
                    <span class="dl-mono">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
                    @if($logs->nextPageUrl())
                        <a href="{{ $logs->nextPageUrl() }}" class="dl-btn dl-cut">Next</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .dockline{
        --radius:2px; --ease:cubic-bezier(.22,.9,.32,1);
        --bg:#F4F0E6; --bg-2:#EAE3D2; --surface:#FFFFFF;
        --ink:#12181C; --ink-soft:#5A6570; --line:rgba(18,24,28,.14);
        --accent:#B96E10; --accent-text:#12181C; --teal-c:#177264; --danger:#B94A10;
        background:var(--bg); color:var(--ink); font-family:'Inter',sans-serif;
        min-height:100vh; padding:32px 0;
    }
    html[data-theme="dark"] .dockline{
        --bg:#0A1420; --bg-2:#0E1926; --surface:#101B27;
        --ink:#ECE6D8; --ink-soft:#93A4B0; --line:rgba(236,230,216,.14);
        --accent:#FFB020; --accent-text:#0A1420; --teal-c:#2FD9C0; --danger:#FF6B4A;
    }
    .dockline *{box-sizing:border-box;}
    .dockline h1{font-family:'Fraunces',serif; font-weight:500; font-size:clamp(26px,3vw,36px); margin:6px 0 10px;}
    .dl-wrap{max-width:1080px; margin:0 auto; padding:0 24px;}
    .dl-eyebrow{font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.16em; text-transform:uppercase; color:var(--accent); font-weight:600; display:block; margin-bottom:4px;}
    .dl-sub{color:var(--ink-soft); font-size:14px; max-width:65ch; line-height:1.6; margin-top:2px;}
    .dl-head{display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between; align-items:flex-end; margin-bottom:20px;}
    .dl-head-actions{display:flex; gap:10px;}

    .dl-btn{font-family:'IBM Plex Mono',monospace; font-size:12.5px; letter-spacing:.06em; text-transform:uppercase; text-decoration:none; display:inline-flex; align-items:center; gap:8px; padding:10px 16px; cursor:pointer; border:none; transition:transform .3s var(--ease);}
    .dl-cut{background:transparent; color:var(--ink); border:1px dashed color-mix(in srgb, var(--ink) 35%, transparent);}
    .dl-cut:hover{background:var(--surface); border-color:var(--ink); transform:translateY(-2px);}

    .dl-filters{display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;}
    .dl-filters select, .dl-filters input[type="text"]{font-family:'IBM Plex Mono',monospace; font-size:12.5px; padding:9px 12px; border:1px solid var(--line); background:var(--surface); color:var(--ink); border-radius:4px;}
    .dl-filters input[type="text"]{flex:1; min-width:180px;}
    .dl-filters input[type="text"]:focus{outline:none; border-color:var(--accent);}

    .dl-day-row td{background:var(--bg-2); font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.1em; text-transform:uppercase; color:var(--ink-soft); padding:8px 16px; border-bottom:1px solid var(--line);}

    .dl-board{background:var(--surface); border:1px solid var(--line); border-radius:4px; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden;}
    html[data-theme="dark"] .dl-board{box-shadow:0 10px 30px rgba(0,0,0,.35);}
    .dl-board-head{padding:14px 20px; border-bottom:1px solid var(--line); font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.12em; text-transform:uppercase; color:var(--ink-soft);}

    .dl-table-scroll{overflow-x:auto;}
    .dl-table{width:100%; border-collapse:collapse; font-size:13px;}
    .dl-table th{text-align:left; font-family:'IBM Plex Mono',monospace; font-size:10.5px; letter-spacing:.06em; text-transform:uppercase; color:var(--ink-soft); padding:10px 16px; border-bottom:1px solid var(--line); white-space:nowrap;}
    .dl-table td{padding:12px 16px; border-bottom:1px solid var(--line); vertical-align:top;}
    .dl-row{cursor:pointer; transition:background .2s;}
    .dl-row:hover{background:var(--bg-2);}
    .dl-mono{font-family:'IBM Plex Mono',monospace; font-size:12px; white-space:nowrap;}
    .dl-truncate{max-width:280px;}
    .dl-pill{display:inline-block; font-family:'IBM Plex Mono',monospace; font-size:11px; background:var(--bg-2); border:1px solid var(--line); border-radius:10px; padding:2px 8px; margin:1px 3px 1px 0;}

    .dl-status{font-family:'IBM Plex Mono',monospace; font-size:11px; letter-spacing:.04em; text-transform:uppercase; padding:3px 9px; border-radius:10px; white-space:nowrap;}
    .dl-status-ok{background:color-mix(in srgb, var(--teal-c) 16%, transparent); color:var(--teal-c);}
    .dl-status-warn{background:color-mix(in srgb, var(--accent) 16%, transparent); color:var(--accent);}
    .dl-status-bad{background:color-mix(in srgb, var(--danger) 16%, transparent); color:var(--danger);}
    .dl-status-muted{background:var(--bg-2); color:var(--ink-soft);}

    .dl-detail-row{display:none;}
    .dl-detail-row.open{display:table-row;}
    .dl-detail{padding:0; border-bottom:1px solid var(--line);}
    .dl-detail-inner{display:flex; padding:16px 20px; background:var(--bg-2); flex-direction:column; gap:12px;}
    .dl-detail p{font-size:13px; line-height:1.6; margin-top:2px;}
    .dl-detail pre{font-family:'IBM Plex Mono',monospace; font-size:11.5px; background:var(--surface); border:1px solid var(--line); border-radius:4px; padding:10px 12px; overflow-x:auto; max-height:220px;}
    .dl-detail-meta{display:flex; gap:16px; font-family:'IBM Plex Mono',monospace; font-size:11px; color:var(--ink-soft); text-transform:uppercase; letter-spacing:.04em;}

    .dl-empty{text-align:center; color:var(--ink-soft); padding:32px 0 !important;}

    .dl-pager{display:flex; align-items:center; gap:14px; padding:14px 20px; border-top:1px solid var(--line);}

    @media (max-width:720px){
        .dl-truncate{max-width:160px;}
    }
</style>
@endsection
