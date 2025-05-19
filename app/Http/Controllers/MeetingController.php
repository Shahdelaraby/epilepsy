<?php

namespace App\Http\Controllers;

use App\Models\{Meeting, Participant};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class MeetingController extends Controller {
    public function index()
    {
        return response()->json(Meeting::with(['user', 'participants'])->get());
    }

    public function store(Request $request)
{

    $request->validate([
        'meeting_category' => 'required|in:schedule,communication',
        'meeting_mode'     => 'required|in:audio,video',
        'title'            => 'required|string',
        'meeting_room'     => 'required_if:meeting_category,schedule|string',
        'description'      => 'nullable|string',
        'start_time'       => 'required_if:meeting_category,schedule|date',
        'end_time'         => 'required_if:meeting_category,schedule|date|after:start_time',
        'time_zone'        => 'nullable|string',
        'link'             => 'nullable|url',
        'schedule'         => 'nullable|in:yes,no',
        'for_later'        => 'nullable|in:yes,no'
    ]);

    $meetingData = [
        'title'            => $request->title,
        'meeting_room'     => $request->meeting_room,
        'description'      => $request->description,
        'user_id'          => auth()->id(),
        'meeting_mode'     => $request->meeting_mode,
        'meeting_category' => $request->meeting_category,
    ];

    if ($request->meeting_category === 'schedule') {
        $meetingData['start_time']  = Carbon::parse($request->start_time);
        $meetingData['end_time']    = Carbon::parse($request->end_time);
        $meetingData['status']      = 'pending';
        $meetingData['schedule']    = 'yes';
        $meetingData['for_later']   = 'yes';
    } else {
        $meetingData['start_time']  = now();
        $meetingData['end_time']    = null;
        $meetingData['status']      = 'live';
        $meetingData['schedule']    = 'no';
        $meetingData['for_later']   = 'no';
    }

    try {
        $event = new Event;
        $event->name = $request->title;
        $event->description = $request->description;
        $event->startDateTime = Carbon::parse($request->start_time);
        $event->endDateTime = Carbon::parse($request->end_time);
        $event->addAttendee(['email' => auth()->user()->email]);

        if ($request->has('attendees'))
        {
            foreach ($request->attendees as $attendeeEmail) {
                $event->addAttendee(['email' => $attendeeEmail]);
            }
        }

        $event->addMeetLink();
        $event->setColorId(9);

        $event->save();

        $events = Event::get();

        $latestEvent = $events->filter(function ($e) use ($request) {
            return $e->name === $request->title &&
                   $e->startDateTime->equalTo(Carbon::parse($request->start_time));
        })->last();

        $meetLink = $latestEvent?->googleEvent['hangoutLink'] ?? null;

        if ($meetLink) {
            $meetingData['link'] = $meetLink;
        }

    } catch (\Exception $e) {
        Log::error('Google Calendar Error: ' . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Error creating Google Calendar event.',
            'error'   => $e->getMessage(),
        ], 500);
    }

    $meeting = Meeting::create($meetingData);

    Participant::create([
        'meeting_id' => $meeting->id,
        'user_id' => auth()->id(), 
        'meeting_mode' => $request->meeting_mode,
    ]);

    if ($request->has('attendees'))
    {
        foreach ($request->attendees as $attendeeEmail)
         {
            $user = User::where('email', $attendeeEmail)->first();

            if ($user) {
                Participant::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $user->id,
                    'meeting_mode' => $request->meeting_mode,
                ]);
            }
        }
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Meeting created successfully',
        'data'    => $meeting
    ], 201);

}
public function join(Request $request)
{

    return response()->json(['message' => 'https://meet.google.com/wrx-iajd-sqc']);
}
}
