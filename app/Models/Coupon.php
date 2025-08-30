<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_coupon')
            ->withTimestamps();
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return now()->gt($this->valid_until);
    }

    public function appliesToCategory(int $categoryId): bool
    {
        return $this->categories()->where('categories.id', $categoryId)->exists();
    }

    public function appliesToProduct(Product $product): bool
    {
        return $this->appliesToCategory($product->category_id);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where('valid_from', '<=', $now)
            ->where('valid_until', '>=', $now);
    }
}
