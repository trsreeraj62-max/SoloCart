<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path', 
        'image', 
        'title', 
        'subtitle',
        'link', 
        'type', 
        'start_date', 
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = ['image_url'];

    /**
     * Scope for active banners
     * Checks is_active AND date range if set
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                     ->where(function($q) use ($now) {
                         $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                     })
                     ->where(function($q) use ($now) {
                         $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                     });
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
                return $this->image_path;
            }
            return asset('storage/' . $this->image_path);
        }
        return null;
    }
}
