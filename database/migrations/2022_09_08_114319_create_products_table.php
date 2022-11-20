<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('intrest_id')->unsigned()->index();
            $table->foreign('intrest_id')->references('id')->on('intrests')->onDelete('cascade');
            $table->string('description');
            $table->integer('favourite')->default('0');
            $table->integer('dislike')->default('0');
            $table->string('address');
            $table->double('lat');
            $table->double('lng');
           
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
        Schema::dropIfExists('products');
    }
 
}
