<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Inbox extends Model
{
    protected $table = 'inbox';
    public $timestamps = false;

    public function outbox() {
        return $this->hasOne(Sent::class,'message_id');
    }


    public function toArray() {

        if (is_null($this->outbox)){
            $latency = "Undefined";
        }else{
            if (is_null($this->outbox->delivery_time) || is_null($this->timestamp)){
                $latency = "Undefined";
            }else{
                $to  = Carbon::createFromFormat('Y-m-d H:i:s', $this->outbox->delivery_time);
                $from  = Carbon::createFromFormat('Y-m-d H:i:s', $this->timestamp);

                $latency =$to->diffForHumans($from);
            }
        }



        $data = parent::toArray();
        $data['outbox'] = $this->outbox;
        $data['latency'] = $latency;

        return $data;
    }

}
