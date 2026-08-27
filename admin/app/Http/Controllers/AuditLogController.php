<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use HasinHayder\Tyro\Models\AuditLog;

class AuditLogController extends Controller
{
    /**
     * A single, unified activity feed for the RBAC system: the vendor
     * package's own audit log (role/privilege create/update/delete/attach/
     * detach, already populated automatically) merged with this app's own
     * access-key grant/revoke history (recorded via HasTimeline on
     * PrivilegeAccessKey). Two different tables, one chronological feed.
     */
    public function index()
    {
        $auditLogs = AuditLog::with('user:id,name,email')
            ->latest('created_at')
            ->limit(75)
            ->get()
            ->map(fn ($log) => [
                'source' => 'role_privilege',
                'summary' => $log->summary,
                'user' => $log->user?->name ?? 'System',
                'created_at' => $log->created_at,
            ]);

        $accessKeyLogs = Timeline::where('timelineable_type', 'access_key_mapping')
            ->with('causer:id,name')
            ->latest('id')
            ->limit(75)
            ->get()
            ->map(fn ($entry) => [
                'source' => 'access_key',
                'summary' => $entry->title . ' — ' . $entry->description,
                'user' => $entry->causer?->name ?? 'System',
                'created_at' => $entry->created_at,
            ]);

        $activity = $auditLogs
            ->concat($accessKeyLogs)
            ->sortByDesc('created_at')
            ->take(100)
            ->values();

        return view('rbac.audit-log', compact('activity'));
    }
}
