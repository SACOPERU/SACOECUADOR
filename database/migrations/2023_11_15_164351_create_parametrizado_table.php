<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametrizadoTable extends Migration
{
    public function up()
    {
        Schema::create('parametrizado', function (Blueprint $table) {

            $table->id();
            $table->string('name_modelo')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresa_canal');

            $table->unsignedBigInteger('simbolo_moneda_id')->nullable();
            $table->foreign('simbolo_moneda_id')->references('id')->on('pais_moneda');

            $table->unsignedBigInteger('desc_empresa_id')->nullable();
            $table->foreign('desc_empresa_id')->references('id')->on('empresa_canal');
            $table->unsignedBigInteger('canal_id')->nullable();
            $table->foreign('canal_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('desc_canal_id')->nullable();
            $table->foreign('desc_canal_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('subcanal_id')->nullable();
            $table->foreign('subcanal_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('desc_subcanal_id')->nullable();
            $table->foreign('desc_subcanal_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('modelo_negocio_id')->nullable();
            $table->foreign('modelo_negocio_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('bodega_id')->nullable();
            $table->foreign('bodega_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('tipo_distribucion_id')->nullable();
            $table->foreign('tipo_distribucion_id')->references('id')->on('canal_subcanal');

            $table->unsignedBigInteger('lp_visual_id')->nullable();
            $table->foreign('lp_visual_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('desc_lp_visual_id')->nullable();
            $table->foreign('desc_lp_visual_id')->references('id')->on('canal_subcanal');

            $table->unsignedBigInteger('idflexline_visual_id')->nullable();
            $table->foreign('idflexline_visual_id')->references('id')->on('canal_subcanal');


            $table->unsignedBigInteger('idflexline_neto_id')->nullable();
            $table->foreign('idflexline_neto_id')->references('id')->on('canal_subcanal');

            $table->unsignedBigInteger('lp_neto_id')->nullable();
            $table->foreign('lp_neto_id')->references('id')->on('canal_subcanal');
            $table->unsignedBigInteger('desc_lp_neto_id')->nullable();
            $table->foreign('desc_lp_neto_id')->references('id')->on('canal_subcanal');


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametrizado');
    }
}
