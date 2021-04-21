<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGithupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('githups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('githup_id')->unique();
            $table->string('username')->unique();
            $table->string('twitter_username')->nullable();
            $table->string('token')->unique();
            $table->string('location')->nullable();
            $table->mediumText('bio')->nullable();
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('githups');
    }
}
