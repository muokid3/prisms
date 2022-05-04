<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function followup() {
        return $this->hasOne(FollowupQuestion::class);
    }

    public function question() {
        return $this->belongsTo(Question::class,'question_id');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['followup'] = $this->followup;

        return $data;
    }
}
