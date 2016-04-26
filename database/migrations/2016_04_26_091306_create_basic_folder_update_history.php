<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasicFolderUpdateHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_folder_update_history', function(Blueprint $table){
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('benefiter_id')->unsigned();
            $table->integer('medical_location_id')->unsigned();
            $table->date('update_date');
            $table->text('comments');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('benefiter_id')->references('id')->on('benefiters');
            $table->foreign('medical_location_id')->references('id')->on('medical_location_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basic_folder_update_history');
    }
}
