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
            $table->float('AUD', 13, 2)->default('0');
            $table->float('CAD', 13, 2)->default('0');
            $table->float('CHF', 13, 2)->default('0');
            $table->float('DDK', 13, 2)->default('0');
            $table->float('EUR', 13, 2)->default('0');
            $table->float('GBP', 13, 2)->default('0');
            $table->float('JPY', 13, 2)->default('0');
            $table->float('NOK', 13, 2)->default('0');
            $table->float('SEK', 13, 2)->default('0');
            $table->float('USD', 13, 2)->default('0');
            $table->float('BRL', 13, 2)->default('0');
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
