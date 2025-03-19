<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{

public function user() {
    return $this->belongsTo(User::class);
}

public function participants() {
    return $this->hasMany(Participant::class);
}

}
