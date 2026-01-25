<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'psgc_code',
        'region_name',
        'region_code',
    ];

    public function provinces()
    {
        return $this->hasMany(Province::class, 'region_code', 'region_code');
    }
}
