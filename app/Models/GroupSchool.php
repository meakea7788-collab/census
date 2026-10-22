<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupSchool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'director_name',
        'phone',
        'address',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'group_school_id');
    }
}
