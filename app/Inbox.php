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
            $from  = Carbon::createFromFormat('Y-m-d H:i:s', $this->timestamp);
            if (is_null($this->outbox->delivery_time)){
                $latency = "Undefined";
            }else{
                $to  = Carbon::createFromFormat('Y-m-d H:i:s', $this->outbox->delivery_time);

                $latency =$to->diffForHumans($from);
            }
        }



        $data = parent::toArray();
        $data['outbox'] = $this->outbox;
        $data['latency'] = $latency;

        return $data;
    }

}
