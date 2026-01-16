<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'name',
        'price',
        'sort',
    ];

    /**
     * 關聯到 Store（一個品項屬於一個類別）
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
