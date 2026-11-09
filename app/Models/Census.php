<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Census extends Model
{
    use HasFactory;
    protected $table = 'census';

    public function holeHouses(): HasMany
    {
        return $this->hasMany(hole_house::class, 'census_id');
    }
}
