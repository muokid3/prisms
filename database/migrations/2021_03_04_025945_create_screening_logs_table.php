<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreeningLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('screening_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ipno')->nullable();
            $table->string('study');
            $table->string('participant_id');
            $table->string('criterion');
            $table->string('status')->nullable();
            $table->string('user_name')->nullable();
            $table->string('date_screened')->nullable();
            $table->string('event_id')->nullable();
            $table->string('randomised')->nullable();
            $table->string('date_randomised')->nullable();
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
        Schema::dropIfExists('screening_logs');
    }
}
