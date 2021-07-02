<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SiteStudy extends Model
{
    public function site() {
        return $this->belongsTo(Site::class,'site_id');
    }

    public function study() {
        return $this->belongsTo(Study::class,'study_id');
    }

    public function coordinator() {
        return $this->belongsTo(User::class,'study_coordinator');
    }

    public function toArray() {

        $data = parent::toArray();
        $data['study_name'] = $this->study->study;
        $data['study_detail'] = $this->study->study_detail;
        $data['site_name'] = optional($this->site)->site_name;

        return $data;
    }
}
