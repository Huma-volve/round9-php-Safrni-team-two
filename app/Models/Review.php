<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'item_id',
        'rating',
        'title',
        'body',
        'photos_json',
        'status'
    ];

    protected $casts = [
        'photos_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
