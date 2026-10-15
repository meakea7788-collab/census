<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSchool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'director_name',
        'phone',
        'address',
    ];
}
