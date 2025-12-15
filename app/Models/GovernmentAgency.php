<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernmentAgency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for this government agency.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'government_agency_id');
    }

    /**
     * Scope to get only active agencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

