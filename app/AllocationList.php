<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AllocationList extends Model
{
    protected $table = 'allocation_list';

    public function study() {
        return $this->belongsTo(Study::class,'study_id');
    }

    public function staff() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function site() {
        return $this->belongsTo(Site::class,'site_id');
    }

    public function stratum() {
        return $this->belongsTo(Stratum::class,'stratum_id');
    }
}
