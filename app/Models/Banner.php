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
        'start_at', 
        'end_at',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected $appends = ['image_url', 'status', 'is_scheduled_active'];

    public function getStatusAttribute()
    {
        if (!$this->is_active) return 'disabled';
        
        $now = now();
        if ($this->start_at > $now) return 'scheduled';
        if ($this->end_at < $now) return 'expired';
        
        return 'active';
    }

    public function getIsScheduledActiveAttribute()
    {
        $now = now();
        return $this->is_active && $this->start_at <= $now && $this->end_at >= $now;
    }

    /**
     * Scope for active banners
     * Logic: start_at <= now() AND end_at >= now()
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                     ->where('start_at', '<=', $now)
                     ->where('end_at', '>=', $now);
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
