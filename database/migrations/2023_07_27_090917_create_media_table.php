<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('source')->nullable();
            $table->string('type')->nullable();
            $table->string('dir')->nullable();
            $table->string('ext')->nullable();
            $table->string('slug')->nullable();
            $table->string('thumes')->nullable();
            $table->string('status')->nullable();
            $table->string('author')->nullable();
            $table->text('caption')->nullable();
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
        Schema::dropIfExists('media');
    }
}
