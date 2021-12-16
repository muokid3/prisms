<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function answers() {
        return $this->hasMany(Answer::class);
    }

    public function responses() {
        return $this->hasMany(Response::class);
    }

    public function toArray() {
        $data = parent::toArray();
        $data['answers'] = $this->answers;

        return $data;
    }


}
