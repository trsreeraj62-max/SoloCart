<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'discount_percent', 'start_at', 'end_at'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected $appends = ['image_url', 'active_discount'];

    /**
     * Scope for active discounts
     * Logic: start_at <= now() AND end_at >= now()
     */
    public function scopeActiveDiscount($query)
    {
        $now = now();
        return $query->where('discount_percent', '>', 0)
                     ->where('start_at', '<=', $now)
                     ->where('end_at', '>=', $now);
    }

    public function getActiveDiscountAttribute()
    {
        $now = now();
        if ($this->discount_percent > 0 && 
            $this->start_at && $this->start_at <= $now && 
            $this->end_at && $this->end_at >= $now) {
            return [
                'percent' => $this->discount_percent,
                'start_at' => $this->start_at,
                'end_at' => $this->end_at
            ];
        }
        return null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Premium predefined images for common categories
        $placeholders = [
            'mobile'      => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=200&auto=format&fit=crop',
            'phone'       => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=200&auto=format&fit=crop',
            'laptop'      => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=200&auto=format&fit=crop',
            'computer'    => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=200&auto=format&fit=crop',
            'headphone'   => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=200&auto=format&fit=crop',
            'watch'       => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=200&auto=format&fit=crop',
            'camera'      => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=200&auto=format&fit=crop',
            'gaming'      => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=200&auto=format&fit=crop',
            'fruit'       => 'https://images.unsplash.com/photo-1619566636858-adf3ef46400b?q=80&w=200&auto=format&fit=crop',
            'grocery'     => 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=200&auto=format&fit=crop',
            'fashion'     => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=200&auto=format&fit=crop',
            'electronics' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?q=80&w=200&auto=format&fit=crop'
        ];

        foreach ($placeholders as $key => $url) {
            if (stripos($this->name, $key) !== false) {
                return $url;
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f1f3f6&color=2874f0&bold=true&size=128';
    }
}
