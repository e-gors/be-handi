<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortListsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_lists_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('profile_id')->nullable();
            $table->foreignId('post_id')->nullable();
            $table->string('favorite_type');
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
        Schema::dropIfExists('short_lists_tables');
    }
}
