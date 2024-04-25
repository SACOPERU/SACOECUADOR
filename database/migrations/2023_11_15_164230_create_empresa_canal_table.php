<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresaCanalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresa_canal', function (Blueprint $table) {
            $table->id();
            $table->string('empresa')->nullable();
            $table->string('desc_empresa')->nullable();
            $table->string('ruc')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono01')->nullable();
            $table->string('telefono02')->nullable();

            $table->string('correo_finanzas')->nullable();
            $table->string('correo_comercial')->nullable();
            $table->string('correo_operaciones')->nullable();


            $table->string('logo_path')->nullable();

            $table->unsignedBigInteger('pais_id')->nullable();
            $table->foreign('pais_id')->references('id')->on('pais_moneda');

            $table->unsignedBigInteger('moneda_id')->nullable();
            $table->foreign('moneda_id')->references('id')->on('pais_moneda');


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
        Schema::dropIfExists('empresa_canal');
    }
}
