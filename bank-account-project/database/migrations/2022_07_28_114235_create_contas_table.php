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
        Schema::create('contas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('AUD', 13, 2)->default(0)->nullable();
            $table->float('CAD', 13, 2)->default(0)->nullable();
            $table->float('CHF', 13, 2)->default(0)->nullable();
            $table->float('DDK', 13, 2)->default(0)->nullable();
            $table->float('EUR', 13, 2)->default(0)->nullable();
            $table->float('GBP', 13, 2)->default(0)->nullable();
            $table->float('JPY', 13, 2)->default(0)->nullable();
            $table->float('NOK', 13, 2)->default(0)->nullable();
            $table->float('SEK', 13, 2)->default(0)->nullable();
            $table->float('USD', 13, 2)->default(0)->nullable();
            $table->float('BRL', 13, 2)->default(0)->nullable();
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
        Schema::dropIfExists('contas');
    }
};
