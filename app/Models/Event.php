<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'startDateTime',
        'endDateTime',
        'meet_link',
        'attendees', // ممكن يكون عمود JSON
        'meeting_id'
    ];

    protected $casts = [
        'attendees' => 'array',
        'startDateTime' => 'datetime',
        'endDateTime' => 'datetime',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // إضافة الحضور (Attendees) إلى event
    public function addAttendee($attendee)
    {
        $attendees = $this->attendees ?? [];
        $attendees[] = $attendee;
        $this->attendees = $attendees;
    }

    // إضافة رابط Google Meet
    public function addMeetLink($link = null)
    {
        // لو الرابط مش جاى من الريكوست، نعمله توليد وهمي أو من API حقيقي لاحقًا
        $this->meet_link = $link ?? 'https://meet.google.com/example-link';
    }
}
