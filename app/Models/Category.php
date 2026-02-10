<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
   use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'image',
        'editable_fields'
    ];

    protected $casts = [
        'editable_fields' => 'array',
    ];
}