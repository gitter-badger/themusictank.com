<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->string('slug')->index()->unique();
            $table->integer('artist_id')->index()->unsigned();
            $table->integer('year')->nullable()->unsigned();
            $table->integer('month')->nullable()->unsigned();
            $table->integer('day')->nullable()->unsigned();
            $table->boolean('thumbnail')->nullable();
            $table->timestamps();
        });

        Schema::table('albums', function($table) {
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albums');
    }
}
