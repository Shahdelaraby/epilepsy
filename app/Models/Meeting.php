<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Meeting extends Model
{

protected $fillable = ['title', 'meeting_room', 'description', 'start_time', 'end_time', 'time_zone','link', 'status', 'meeting_mode', 'user_id','for_later','schedule','meeting_category'];

public function getLocalStartTimeAttribute()
{
    return Carbon::parse($this->start_time)->setTimezone($this->time_zone);
}

public function user() {
    return $this->belongsTo(User::class);
}

public function participants() {
    return $this->hasMany(Participant::class);
}

}
