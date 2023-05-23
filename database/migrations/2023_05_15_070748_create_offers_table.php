<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); //owner of the offer
            $table->foreignId('profile_id'); // invited worker
            $table->foreignId('post_id')->nullable();
            $table->string('title');
            $table->string('type');
            $table->string('days')->nullable();
            $table->decimal('rate', 8, 2)->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->text('instructions')->nullable();
            $table->text('images')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
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
        Schema::dropIfExists('offers');
    }
}
