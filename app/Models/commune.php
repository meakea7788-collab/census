<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class commune extends Model
{
    use HasFactory;

    protected $table = 'commune';

    protected $fillable = ['name', 'district_id'];

    public function villages(): HasMany
    {
        return $this->hasMany(village::class, 'commune_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
