<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    public function respondent() {
        return $this->belongsTo(User::class,'user_id');
    }
    public function answer() {
        return $this->belongsTo(Answer::class);
    }
}
