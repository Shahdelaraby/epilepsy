<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class MeetingController extends Controller {
    // عرض جميع الاجتماعات
    public function index() {
        return response()->json(Meeting::with(['user', 'participants'])->get());
    }

    // إنشاء اجتماع جديد
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string',
            'meeting_room' => 'nullable|string',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'link' => 'required|string',
            'status' => 'pending',
            'type' => 'required|in:video,audio',
        ]);

        $meetingLink = $this->getIntegrationMeetingLink(); // افتراضياً يتم جلب الرابط من دالة خارجية
        $meeting = Meeting::create([
            'title' => $request->title,
            'meeting_room' => $request->meeting_room,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'link' => $request->link,
            'status' => 'pending',
            'type' => $request->type,
            'user_id' => 1, // تعيين مستخدم افتراضي أثناء الاختبار
        ]);



        return response()->json(['message' => 'Meeting created successfully', 'meeting' => $meeting], 201);
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
        'user_id' => 1, // تعيين مستخدم افتراضي أثناء الاختبار
        'type' => $request->type,
    ]);

    return response()->json(['message' => 'Joined meeting successfully!']);
}


public function startMeet(Request $request, $id)
{
    $meeting = Meeting::findOrFail($id);

    if ($meeting->status !== 'pending') {
        return response()->json(['message' => 'Meeting is not in pending state'], 400);
    }

    // جلب الرابط من API التكامل




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
    // هنا يتم استدعاء API لجلب رابط الاجتماع من خدمة خارجية
    return "https://external-meeting.com/room12345"; // استبدلها باللوجيك الفعلي
}
}
