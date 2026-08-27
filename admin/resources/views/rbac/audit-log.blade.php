@extends('layouts.app')

@section('content')
<style>
    .al-wrap { max-width: 900px; margin: 0 auto; padding: 24px; }
    .al-header { margin-bottom: 20px; }
    .al-header h1 { font-size: 22px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0; }
    .al-header p { color: var(--text-secondary); font-size: 14px; margin: 0; }

    .al-card { background: var(--card); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--card-shadow); overflow: hidden; }
    .al-row { display: flex; gap: 14px; padding: 14px 18px; border-bottom: 1px solid var(--border-color); align-items: flex-start; }
    .al-row:last-child { border-bottom: none; }
    .al-icon {
        width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; margin-top: 2px;
    }
    .al-icon.role_privilege { background: rgba(99,102,241,.15); color: #6366f1; }
    .al-icon.access_key { background: rgba(234,179,8,.15); color: #ca8a04; }
    .al-body { flex: 1; min-width: 0; }
    .al-summary { font-size: 14px; color: var(--text-primary); margin: 0 0 4px 0; word-break: break-word; }
    .al-meta { font-size: 12px; color: var(--text-secondary); }
    .al-empty { padding: 40px 20px; text-align: center; color: var(--text-secondary); }
</style>

<div class="al-wrap">
    <div class="al-header">
        <h1>Activity Log</h1>
        <p>Recent role, privilege, and access key changes — newest first.</p>
    </div>

    <div class="al-card">
        @forelse ($activity as $entry)
            <div class="al-row">
                <div class="al-icon {{ $entry['source'] }}">
                    <i data-lucide="{{ $entry['source'] === 'access_key' ? 'key' : 'shield' }}" style="width:16px;height:16px"></i>
                </div>
                <div class="al-body">
                    <p class="al-summary">{{ $entry['summary'] }}</p>
                    <div class="al-meta">{{ $entry['user'] }} • {{ \Illuminate\Support\Carbon::parse($entry['created_at'])->diffForHumans() }} ({{ \Illuminate\Support\Carbon::parse($entry['created_at'])->format('M d, Y H:i') }})</div>
                </div>
            </div>
        @empty
            <div class="al-empty">No activity recorded yet.</div>
        @endforelse
    </div>
</div>
@endsection
