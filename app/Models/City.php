<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'city_code',
        'city_name',
        'psgc_code',
        'province_code',
        'region_code',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_code', 'region_code');
    }

    public function barangays()
    {
        return $this->hasMany(Barangay::class, 'city_code', 'city_code');
    }
}
