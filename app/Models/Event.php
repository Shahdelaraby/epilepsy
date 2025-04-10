<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $fillable = [
        'meeting_id',
        'summary',
        'description',
        'start',
        'end',
        'attendees',
        'meet_link',
        'event_id'
    ];

    protected $casts = [
        'attendees' => 'array',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

}
