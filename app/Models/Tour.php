<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'main_image',
        'duration',
        'location',
        'stars',
        'recommended',
        // 'created_by',
    ];

    protected $casts = [
        'recommended' => 'boolean',
    ];

    // Relations
    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }
}
