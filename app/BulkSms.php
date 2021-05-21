<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkSms extends Model
{
    public function creator() {
        return $this->belongsTo(User::class,'created_by');
    }
}
