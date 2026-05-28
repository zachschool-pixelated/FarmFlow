<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'profile_picture',
        'address',
        'province',
        'city',
        'barangay',
        'postal_code',
        'street_address',
        'is_active',
        'is_blacklisted',
        'blacklist_reason',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_blacklisted' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Supplier $supplier): void {
            if (blank($supplier->supplier_code)) {
                $supplier->supplier_code = static::generateSupplierCode();
            }

            $supplier->is_active = $supplier->is_active ?? true;
            $supplier->is_blacklisted = $supplier->is_blacklisted ?? false;
        });
    }

    public static function generateSupplierCode(): string
    {
        $nextId = ((int) (static::max('id') ?? 0)) + 1;

        return 'SUP-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_blacklisted) {
            return 'Blacklisted';
        }

        return $this->is_active ? 'Active' : 'Inactive';
    }
}