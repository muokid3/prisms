<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteContact extends Model
{
    public function role() {
        return $this->belongsTo(UserGroup::class,'user_group');
    }

    public function hospital() {
        return $this->belongsTo(RedcapSite::class,'redcap_site_id');
    }
}
