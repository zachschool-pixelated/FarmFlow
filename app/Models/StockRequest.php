<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

class StockRequest extends Model
{
    use Auditable;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'user_id',
        'quantity_requested',
        'status',
        'notes',
        'shipped_at',
        'expected_delivery_at',
    ];

    protected $casts = [
        'shipped_at'           => 'datetime',
        'expected_delivery_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
