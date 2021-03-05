<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_studies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->unsignedBigInteger('study_id');
            $table->foreign('study_id')->references('id')->on('studies');

            $table->unsignedBigInteger('study_coordinator');
            $table->foreign('study_coordinator')->references('id')->on('users');

            $table->date('date_initiated');
            $table->string('status');

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
        Schema::dropIfExists('site_studies');
    }
}
