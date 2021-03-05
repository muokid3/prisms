<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_reports', function (Blueprint $table) {
            $table->id();
            $table->string('sms_centre')->nullable();
            $table->string('timestamp');
            $table->string('destination')->nullable();
            $table->string('source')->nullable();
            $table->string('service')->nullable();
            $table->string('url')->nullable();
            $table->string('mask')->nullable();
            $table->string('status')->nullable();
            $table->string('boxc')->nullable();
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
        Schema::dropIfExists('delivery_reports');
    }
}
