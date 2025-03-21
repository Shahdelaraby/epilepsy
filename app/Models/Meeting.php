<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{

protected $fillable = ['title', 'meeting_room', 'description', 'start_time', 'end_time', 'link', 'status', 'type', 'user_id'];
public function user() {
    return $this->belongsTo(User::class);
}

public function participants() {
    return $this->hasMany(Participant::class);
}

}
