<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_activities', 'activity_id', 'user_id');
    }
}
