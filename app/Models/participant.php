<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    protected $fillable = [
        'meeting_id',
        'user_id',
        'meetin_mode'
    ];

public function meeting() {
    return $this->belongsTo(Meeting::class);
}

public function user() {
    return $this->belongsTo(User::class);
}
}
