<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id'); // owner of the post
            $table->string('title');
            $table->text('description');
            $table->text('skills')->nullable();
            $table->string('category');
            $table->string('position');
            $table->string('job_type');
            $table->string('days')->nullable();
            $table->decimal('rate', 8, 2)->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->text('locations')->nullable();
            $table->text('questions')->nullable();
            $table->text('images')->nullable();
            $table->string('post_url');
            $table->enum('status', ['drafted', 'posted', 'contracted'])->default('posted');
            $table->enum('visibility', ['Public', 'Private'])->default('Public');
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
        Schema::dropIfExists('posts');
    }
}
