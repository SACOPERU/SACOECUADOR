<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaisMonedaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pais_moneda', function (Blueprint $table) {
            $table->id();

            $table->enum('desc_pais', ["Perú","Ecuador","United State"]);

            $table->enum('pais', ["PE","EC","US"]);

            $table->enum('moneda', ["PEN", "USD"]);
            $table->enum('simbolo_moneda', ["$","S/"]);
            $table->string('desc_moneda');



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
        Schema::dropIfExists('pais_moneda');
    }
}
