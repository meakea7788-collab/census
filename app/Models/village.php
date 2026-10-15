<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class village extends Model
{
    use HasFactory;

    protected $table = 'village';

    protected $fillable = ['name', 'commune_id'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(commune::class, 'commune_id');
    }
}
