<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->string('message_id')->nullable();
            $table->dateTime('delivery_time')->nullable();
            $table->string('status')->nullable();
            $table->text('text');
            $table->string('destination');
            $table->dateTime('timestamp');
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
        Schema::dropIfExists('sent');
    }
}
