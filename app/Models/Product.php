<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'discount_percent',
        'discount_start_date',
        'discount_end_date',
        'specifications',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_percent' => 'integer',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];

    protected $appends = ['image_url', 'current_price', 'is_discount_active'];

    /**
     * Scope for active products
     * filters by is_active flag AND stock availability
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('stock', '>', 0);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Determine if discount is currently valid based on dates
     */
    public function getIsDiscountActiveAttribute()
    {
        if (!$this->discount_percent || $this->discount_percent <= 0) {
            return false;
        }

        $now = now();
        
        // If dates are null, assume always active if percent > 0
        if (!$this->discount_start_date && !$this->discount_end_date) {
            return true;
        }

        if ($this->discount_start_date && $this->discount_start_date->isFuture()) {
            return false;
        }

        if ($this->discount_end_date && $this->discount_end_date->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Calculate effective price
     */
    public function getCurrentPriceAttribute()
    {
        if ($this->is_discount_active) {
            return round($this->price * (1 - ($this->discount_percent / 100)), 2);
        }
        return $this->price;
    }

    public function getImageUrlAttribute()
    {
        if ($this->images && $this->images->count() > 0) {
            $path = $this->images->first()->image_path;
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            return asset('storage/' . $path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f1f3f6&color=2874f0&bold=true&size=300';
    }
}
