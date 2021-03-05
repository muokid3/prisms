<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public function studies() {
        return $this->hasMany(SiteStudy::class,'site_id');
    }
}
