<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\village;

class hole_house extends Model
{
    use HasFactory;

    protected $table = 'holehouse';

    protected $fillable = [
        'census_id',
        'hole_number',
        'leader_family_name',
        'phone_number',
        'address',
        'village_id',
        'status',
    ];

    public function census(): BelongsTo
    {
        return $this->belongsTo(Census::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(village::class, 'village_id');
    }

    public function families(): HasMany
    {
        return $this->hasMany(family::class, 'holehouse_id');
    }
}
