<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class participant extends Model
{

public function meeting() {
    return $this->belongsTo(Meeting::class);
}

public function user() {
    return $this->belongsTo(User::class);
}
}
