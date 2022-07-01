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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('handle')->unique();
            $table->string('email')->unique();
            $table->string('mobile')->unique()->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable(); //phone code
            $table->longText('btc_wallet')->nullable(); //btc wallet
            $table->longText('mula_wallet')->nullable(); //mula wallet
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('password');
            $table->string('stream_token')->nullable();
            $table->string('two_factor_code')->nullable();
            $table->string('referral_code')->nullable();
            $table->string('referred_code')->nullable();
            $table->integer('invite_points')->nullable();
            $table->boolean('can_receive_newsletter')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
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
