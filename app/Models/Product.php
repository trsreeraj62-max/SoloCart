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
        'specifications'
    ];

    /**
     * Scope for active products (could be status based, but here we just check stock)
     */
    public function scopeActive($query)
    {
        return $query->where('stock', '>', 0);
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

    public function getImageUrlAttribute()
    {
        if ($this->images && $this->images->first()) {
            return asset('storage/' . $this->images->first()->image_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=f1f3f6&color=2874f0&bold=true&size=300';
    }
}
