<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operacoes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_conta')->unsigned();
            $table->integer('tipo');  //(1) Depósito - (2) Saque
            $table->integer('moeda'); //(1) AUD - (2) CAD - (3) CHF - (4) DKK - (5) EUR - (6) GBP
                                      //(7) JPY - (8) NOK - (9) SEK - (10) USD (11) BRL
            $table->float('valor', 13, 2)->default('0');
            $table->foreign('id_conta')->references('id')->on('contas');
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
        Schema::dropIfExists('operacoes');
    }
};
