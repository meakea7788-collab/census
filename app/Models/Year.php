<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $table = 'year';

    protected $fillable = ['year', 'description', 'is_active'];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
    ];
}
