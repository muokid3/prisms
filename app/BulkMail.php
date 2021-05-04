<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkMail extends Model
{
    public function group() {
        return $this->belongsTo(UserGroup::class,'user_group');
    }

    public function creator() {
        return $this->belongsTo(User::class,'created_by');
    }
}
