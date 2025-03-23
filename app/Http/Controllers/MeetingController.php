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
    public function index()
    {
        return response()->json(Meeting::with(['user', 'participants'])->get());
    }
    #=================================================================================#
    // إنشاء اجتماع جديد
    public function store(Request $request)
    {
        #=== validation =====#
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

        #== prepare data  to store in db ====#
        #common Data
        $meetingData =
        [
            'title'            => $request->title,
            'meeting_room'     =>  'Defualt Romm Until Integrate With Api',
            'description'      => $request->description,
            'user_id'          => auth()->id(),                        // Based on Autnetication
            'time_zone'        => $request->time_zone ?? 'UTC',
            'link'             => $request->link,                      //from request Until Integrate With Api
            'meeting_mode'     => $request->meeting_mode,
            'meeting_category' => $request->meeting_category,
        ];

        if ($request->meeting_category === 'schedule')
        {
            $meetingData['start_time']  = $request->start_time;
            $meetingData['end_time']    = $request->end_time;
            $meetingData['status']      = 'pending';
            $meetingData['schedule']    = 'yes';
            $meetingData['for_later']   = 'yes';
        }
        else
        {
            #communication
            $meetingData['start_time']  = now();
            $meetingData['end_time']    = null;
            $meetingData['status']      = 'live';   // or pending based on ui/ ux
            $meetingData['schedule']    = 'no';
            $meetingData['for_later']   = 'no';
        }

        #===create Meeting==#
        $meeting = Meeting::create($meetingData);

        #=== response =====#
        return response()->json([
            'status'  => 'success',
            'message' => 'Meeting created successfully',
            'data'    => $meeting
        ], 201);
    }
    #=================================================================================#
    // عرض تفاصيل اجتماع معين
    public function show($id)
    {
        $meeting = Meeting::with(['user', 'participants'])->find($id);

        if (!$meeting) {
            return response()->json(['error' => 'Meeting not found'], 404);
        }

        return response()->json($meeting);
    }
    #=================================================================================#
    // الانضمام لاجتماع
    public function join(Request $request, $id)
    {
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
    #=================================================================================#
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
    #=================================================================================#
    public function end($id)
    {
        $meeting = Meeting::findOrFail($id);

        if ($meeting->status !== 'live') {
            return response()->json(['message' => 'Meeting is not live'], 400);
        }

        $meeting->update(['status' => 'end']);

        return response()->json(['message' => 'Meeting ended', 'meeting' => $meeting]);
    }
    #=================================================================================#
    public function cancel($id)
    {
        $meeting = Meeting::findOrFail($id);

        if ($meeting->status === 'end') {
            return response()->json(['message' => 'Cannot cancel a finished meeting'], 400);
        }

        $meeting->update(['status' => 'canceled']);

        return response()->json(['message' => 'Meeting canceled', 'meeting' => $meeting]);
    }
    #=================================================================================#
    private function getIntegrationMeetingLink()
    {

        return "https://external-meeting.com/room12345";
    }
}
