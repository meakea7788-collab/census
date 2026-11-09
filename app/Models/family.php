<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class family extends Model
{
    use HasFactory;
    protected $table = 'family';
    protected $fillable = [
        'holehouse_id',
        'family_name',
        'gender',
        'date_of_birth',
        'relationship',
        'job',
        'moved_to_address',
        'is_deceased',
    ];

    protected $casts = [
        'is_deceased' => 'boolean',
    ];

    public function holehouse()
    {
        return $this->belongsTo(hole_house::class, 'holehouse_id');
    }

}
