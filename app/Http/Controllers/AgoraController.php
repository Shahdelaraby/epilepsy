<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yasser\Agora\RtcTokenBuilder;

class AgoraController extends Controller
{
    public function generateToken(Request $request)
    {
        $request->validate([
            'channelName' => 'required|string',
        ]);

        $appID = config('agora.app_id');
        $appCertificate = config('agora.app_certificate');
        $channelName = $request->channelName;
        $uid = 0; // خلي 0 لو فلاتر هو اللي هيختار الـ UID
        $role = 1; // 1 = Publisher
        $expireTimeInSeconds = 3600; // مثلاً ساعة
        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs
        );

        return response()->json([
            'token' => $token,
            'channelName' => $channelName,
            'uid' => $uid,
            'expire_at' => $privilegeExpiredTs
        ]);
    }
}
