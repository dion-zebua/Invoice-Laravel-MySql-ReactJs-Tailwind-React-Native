<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $guarded = [
        'id',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
