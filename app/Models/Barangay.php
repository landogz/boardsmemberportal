<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $fillable = [
        'brgy_code',
        'brgy_name',
        'city_code',
        'province_code',
        'region_code',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'city_code');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'province_code');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_code', 'region_code');
    }
}
