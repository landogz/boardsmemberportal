<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'province_code',
        'province_name',
        'psgc_code',
        'region_code',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_code', 'region_code');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'province_code', 'province_code');
    }
}
