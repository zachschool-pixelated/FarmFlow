<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductEditRequest extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'user_id',
        'status',
        'reason',
        'requested_changes',
        'original_values',
        'reviewer_id',
        'reviewer_note',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_changes' => 'array',
        'original_values'   => 'array',
        'reviewed_at'       => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}
