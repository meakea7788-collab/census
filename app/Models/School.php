<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    use HasFactory;

    protected $table = 'schools';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'director_name',
        'group_school_id',
    ];

    public function groupSchool(): BelongsTo
    {
        return $this->belongsTo(GroupSchool::class, 'group_school_id');
    }
}
