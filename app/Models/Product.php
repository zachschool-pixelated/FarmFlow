<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, Auditable, SoftDeletes;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'description',
        'unit',
        'price',
        'cost_price',
        'stock_quantity',
        'reorder_level',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
    ];

    protected $appends = [
        'stock_status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Out of Stock';
        }

        if ($this->stock_quantity <= $this->reorder_level) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->reorder_level;
    }
}