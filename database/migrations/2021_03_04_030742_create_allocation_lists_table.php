<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_list', function (Blueprint $table) {
            $table->id();
            $table->integer('sequence');

            $table->unsignedBigInteger('study_id');
            $table->foreign('study_id')->references('id')->on('studies');

            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->unsignedBigInteger('stratum_id');
            $table->foreign('stratum_id')->references('id')->on('strata');

            $table->string('allocation');
            $table->string('participant_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('date_randomised')->nullable();
            $table->string('ipno')->nullable();


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
        Schema::dropIfExists('allocation_list');
    }
}
