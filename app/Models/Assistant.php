<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assistant_id',
        'name',
        'description',
        'model',
        'user_id'
    ];

    /**
     * Get the user that owns the assistant.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
