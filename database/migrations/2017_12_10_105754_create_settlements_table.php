<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id')->onDdelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->string('family');
            $table->string('mobile', 12);
            $table->string('zp')->nullable(false);
            $table->integer('purseId')->nullable(false);
            $table->string('iban')->nullable(false);
            $table->string('transaction_public_id');
            $table->string('transfer_ref_id');
            $table->string('withdraw_ref_id');
            $table->double('amount')->nullable(false)->default(0);
            $table->text('description');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('settlements');
    }
}
