<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'type', 'value', 'valid_until'];

    protected $casts = [
        'valid_until' => 'datetime',
    ];
}
