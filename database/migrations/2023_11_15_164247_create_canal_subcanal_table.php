<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanalSubcanalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canal_subcanal', function (Blueprint $table) {
            $table->id();

            $table->string('canal')->nullable();
            $table->string('desc_canal')->nullable();
            $table->string('subcanal')->nullable();
            $table->string('desc_subcanal')->nullable();
            $table->string('modelo_negocio')->nullable();
            $table->string('bodega')->nullable();
            $table->string('tipo_distribucion')->nullable();


            $table->string('idflexline_visual')->nullable();
            $table->string('lp_visual')->nullable();
            $table->string('desc_lp_visual')->nullable();


            $table->string('idflexline_neto')->nullable();
            $table->string('lp_neto')->nullable();
            $table->string('desc_lp_neto')->nullable();

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
        Schema::dropIfExists('canal_subcanal');
    }
}
