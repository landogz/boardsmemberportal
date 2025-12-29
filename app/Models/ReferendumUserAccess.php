<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferendumUserAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'referendum_id',
        'user_id',
    ];

    /**
     * Get the referendum
     */
    public function referendum()
    {
        return $this->belongsTo(Referendum::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
