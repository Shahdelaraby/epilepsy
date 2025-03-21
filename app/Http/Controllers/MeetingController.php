<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Participant;
use Illuminate\Http\Request;
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
            'type' => 'required|in:video,audio',
        ]);

        $meeting = Meeting::create([
            'title' => $request->title,
            'meeting_room' => $request->meeting_room,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'link' => $request->link,
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
        ]);

        return response()->json(['message' => 'Joined meeting successfully!']);
    }
}
