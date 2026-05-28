<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAuditAction('created');
        });

        static::updated(function ($model) {
            $model->logAuditAction('updated');
        });

        static::deleted(function ($model) {
            $model->logAuditAction('deleted');
        });
    }

    protected function logAuditAction($action)
    {
        $oldValues = [];
        $newValues = [];

        if ($action === 'updated') {
            $oldValues = array_intersect_key($this->getOriginal(), $this->getChanges());
            $newValues = $this->getChanges();
        } elseif ($action === 'created') {
            $newValues = $this->getAttributes();
        } elseif ($action === 'deleted') {
            $oldValues = $this->getAttributes();
        }

        // Avoid logging empty updates
        if ($action === 'updated' && empty($newValues)) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => request()->ip(),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
