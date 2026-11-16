<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Census extends Model
{
    use HasFactory;
    protected $table = 'census';

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function groupSchool(): BelongsTo
    {
        return $this->belongsTo(GroupSchool::class, 'group_school_id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(village::class, 'village_id');
    }

    public function holeHouses(): HasMany
    {
        return $this->hasMany(hole_house::class, 'census_id');
    }
}
