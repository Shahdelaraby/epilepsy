<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class MeetingController extends Controller {
    // عرض جميع الاجتماعات
    public function index() {
        return response()->json(Meeting::with(['user', 'participants'])->get());
    }

    // إنشاء اجتماع جديد
    public function store(Request $request)
{

    $request->validate([

        'type'           => 'required|in:schedule,communication',
        'meeting_mode'   => 'required|in:audio,video',
        'title'          => 'required|string',
        'meeting_room'   => 'required_if:type,schedule|string|nullable',
        'description'    => 'nullable|string',
        'user_id'        => 'required|exists:users,id',
        'start_time'     => 'nullable|date',
        'end_time'       => 'nullable|date',
        'time_zone'      => 'nullable|string',
        'link'           => 'nullable|url',
        'schedule'       => 'nullable|in:yes,no',
        'for_later'      => 'nullable|in:yes,no'
    ]);

    if ($request->type === 'schedule') {
        $meeting = Meeting::create([
            'title'         => $request->title,
            'meeting_room'  => $request->meeting_room,
            'description'   => $request->description,
            'user_id'       => $request->user_id,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'time_zone'     => $request->time_zone ?? 'UTC',
            'link'          => $request->link,
            'type'          => $request->meeting_mode,
            'status'        => 'pending'
        ]);
        $responseData = $meeting->toArray();
    } else {
        // (communication)
        $meeting = Meeting::create([
            'title'         => $request->title,
            'description'   => $request->description,
            'user_id'       => $request->user_id,
            'start_time'    => now(),
            'end_time'      => null,
            'time_zone'     => $request->time_zone ?? 'UTC',
            'link'          => $request->link,
            'type'          => $request->meeting_mode,
            'status'        => 'live'
        ]);

        $responseData = $meeting->toArray();
        $responseData['meeting_room'] = null;
        $responseData['end_time'] = null;
        $responseData['schedule']  = $request->input('schedule', 'no');
        $responseData['for_later'] = $request->input('for_later', 'no');
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'Meeting created successfully',
        'data'    => $responseData
    ]);
}
    // عرض تفاصيل اجتماع معين
    public function show($id) {
        $meeting = Meeting::with(['user', 'participants'])->find($id);

        if (!$meeting) {
            return response()->json(['error' => 'Meeting not found'], 404);
        }

        return response()->json($meeting);
    }

    // الانضمام لاجتماع
   public function join(Request $request, $id) {
    $meeting = Meeting::find($id);

    if (!$meeting) {
        return response()->json(['error' => 'Meeting not found'], 404);
    }

    $request->validate([
        'type' => 'required|in:Video,Audio',
    ]);

    Participant::create([
        'meeting_id' => $meeting->id,
        'user_id' => 1, 
        'type' => $request->type,
    ]);

    return response()->json(['message' => 'Joined meeting successfully!']);
}


public function start(Request $request, $id)
{
    $meeting = Meeting::findOrFail($id);

    if ($meeting->status !== 'pending') {
        return response()->json(['message' => 'Meeting is not in pending state'], 400);
    }


    $meeting->status = 'live';
    $meeting->save();


    return response()->json(['message' => 'Meeting started', 'meeting' => $meeting]);
}




 public function end($id)
{
    $meeting = Meeting::findOrFail($id);

    if ($meeting->status !== 'live') {
        return response()->json(['message' => 'Meeting is not live'], 400);
    }

    $meeting->update(['status' => 'end']);

    return response()->json(['message' => 'Meeting ended', 'meeting' => $meeting]);
}
public function cancel($id)
{
    $meeting = Meeting::findOrFail($id);

    if ($meeting->status === 'end') {
        return response()->json(['message' => 'Cannot cancel a finished meeting'], 400);
    }

    $meeting->update(['status' => 'canceled']);

    return response()->json(['message' => 'Meeting canceled', 'meeting' => $meeting]);
}


private function getIntegrationMeetingLink()
{

    return "https://external-meeting.com/room12345";
}
}
