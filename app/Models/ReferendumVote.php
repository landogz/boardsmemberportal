<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferendumVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'referendum_id',
        'user_id',
        'vote',
    ];

    /**
     * Get the referendum this vote belongs to
     */
    public function referendum()
    {
        return $this->belongsTo(Referendum::class);
    }

    /**
     * Get the user who cast this vote
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
