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
            $table->foreignId('fbcode_id')->nullable($value = true);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nick_name')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_subscription')->default(1);
            $table->string('telephone')->nullable($value = true);
            $table->string('street')->nullable($value = true);
            $table->string('house_number')->nullable($value = true);
            $table->string('city')->nullable($value = true);
            $table->string('post_code')->nullable($value = true);
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
