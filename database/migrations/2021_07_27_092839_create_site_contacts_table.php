<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_contacts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('redcap_site_id');
            $table->foreign('redcap_site_id')->references('id')->on('redcap_sites');

            $table->unsignedBigInteger('user_group');
            $table->foreign('user_group')->references('id')->on('user_groups');

            $table->string('contact_first_name');
            $table->string('contact_full_name');
            $table->string('contact_phone_no');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_contacts');
    }
}
