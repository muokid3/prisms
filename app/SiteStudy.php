<?php

namespace App;

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
}
