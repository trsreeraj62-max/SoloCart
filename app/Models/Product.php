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
        'discount_type',
        'discount_value',
        'discount_percent',
        'discount_start_date',
        'discount_end_date',
        'specifications',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_percent' => 'integer',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];

    protected $appends = [
        'image_url', 
        'current_price', 
        'is_discount_active', 
        'discount_label', 
        'savings_amount'
    ];

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
     * Determine if discount is currently valid based on dates and value
     */
    public function getIsDiscountActiveAttribute()
    {
        // Prioritize discount_value if set, otherwise fallback to discount_percent
        $hasValue = ($this->discount_type === 'flat' && $this->discount_value > 0) || 
                    ($this->discount_type === 'percentage' && $this->discount_value > 0) ||
                    ($this->discount_percent > 0);

        if (!$hasValue || !$this->is_active) {
            return false;
        }

        $now = now();
        
        // If start date is set and is in future, not active
        if ($this->discount_start_date && $this->discount_start_date->isFuture()) {
            return false;
        }

        // If end date is set and is past, not active
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
        if (!$this->is_discount_active) {
            return $this->price;
        }

        if ($this->discount_type === 'flat' && $this->discount_value > 0) {
            return max(0, round($this->price - $this->discount_value, 2));
        }

        // Use discount_value as percentage if type is percentage and value > 0
        $percent = ($this->discount_type === 'percentage' && $this->discount_value > 0) 
            ? $this->discount_value 
            : $this->discount_percent;

        return round($this->price * (1 - ($percent / 100)), 2);
    }

    /**
     * Human-friendly discount label (e.g. "20% OFF" or "₹500 OFF")
     */
    public function getDiscountLabelAttribute()
    {
        if (!$this->is_discount_active) {
            return null;
        }

        if ($this->discount_type === 'flat' && $this->discount_value > 0) {
            return "₹" . number_format((float) $this->discount_value) . " OFF";
        }

        $percent = ($this->discount_type === 'percentage' && $this->discount_value > 0) 
            ? $this->discount_value 
            : $this->discount_percent;

        return round($percent) . "% OFF";
    }

    /**
     * Amount saved
     */
    public function getSavingsAmountAttribute()
    {
        return round($this->price - $this->current_price, 2);
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
