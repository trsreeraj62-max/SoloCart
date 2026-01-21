<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path', 
        'image',      // Allow both image and image_path
        'title', 
        'subtitle',
        'link', 
        'type', 
        'start_date', 
        'end_date'
    ];

    protected $appends = ['image_url'];

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
