<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRestorationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'reason',
        'status',
        'admin_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function auditable()
    {
        return $this->morphTo('model');
    }
}
