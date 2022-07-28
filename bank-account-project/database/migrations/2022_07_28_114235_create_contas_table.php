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
            $table->float('dolar_australiano', 13, 2)->default('0');
            $table->float('dolar_canadense', 13, 2)->default('0');
            $table->float('franco_suico', 13, 2)->default('0');
            $table->float('coroa_dinamarquesa', 13, 2)->default('0');
            $table->float('euro', 13, 2)->default('0');
            $table->float('libra_esterlina', 13, 2)->default('0');
            $table->float('iene', 13, 2)->default('0');
            $table->float('coroa_norueguesa', 13, 2)->default('0');
            $table->float('coroa_sueca', 13, 2)->default('0');
            $table->float('dolar_eua', 13, 2)->default('0');
            $table->float('real', 13, 2)->default('0');
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
