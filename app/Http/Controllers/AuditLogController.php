<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        // Require strict admin access (managers cannot view audit logs)
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $logs = AuditLog::with(['user', 'auditable'])
            ->latest()
            ->paginate(15);

        return view('audit-logs.index', compact('logs'));
    }

    public function revert(AuditLog $auditLog): \Illuminate\Http\RedirectResponse
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        if ($auditLog->action !== 'updated') {
            return redirect()->route('audit-logs.index')->with('error', 'Only update actions can be reverted.');
        }

        $model = $auditLog->auditable;

        if (!$model) {
            return redirect()->route('audit-logs.index')->with('error', 'The target record no longer exists.');
        }

        // Apply old values
        foreach ($auditLog->old_values as $key => $value) {
            $model->{$key} = $value;
        }
        $model->save();

        return redirect()->route('audit-logs.index')->with('status', 'Changes successfully reverted.');
    }
}
