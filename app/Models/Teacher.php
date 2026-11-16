<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Teacher extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'teacher';

    protected $fillable = [
        'name',
        'sex',
        'position',
        'phone',
        'school_id',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function censuses(): BelongsToMany
    {
        return $this->belongsToMany(Census::class, 'census_teacher', 'teacher_id', 'census_id');
    }
}
