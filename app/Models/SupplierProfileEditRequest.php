<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProfileEditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'status',
        'requested_changes',
        'original_data',
        'reviewed_by_id',
        'reviewed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'requested_changes' => 'array',
            'original_data' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }
}
