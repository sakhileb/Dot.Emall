<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreCategory extends Model
{
    protected $table = 'store_categories';

    protected $fillable = ['name', 'slug', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StoreCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(StoreCategory::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
