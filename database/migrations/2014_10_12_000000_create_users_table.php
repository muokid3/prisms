<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_group');
            $table->foreign('user_group')->references('id')->on('user_groups');

            $table->string('phone_no')->unique();
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name');

            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->boolean('active')->default(true);

            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
