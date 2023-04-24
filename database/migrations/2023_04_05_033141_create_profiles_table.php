<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('background')->nullable();
            $table->string('profile_url')->nullable();
            $table->string('background_url')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->string('address');
            $table->string('availability')->nullable();
            $table->string('progress')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
