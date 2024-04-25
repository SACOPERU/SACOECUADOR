<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_id');
            $table->string('numero_documento');
            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->unsignedBigInteger('departamento_id');
            $table->unsignedBigInteger('provincia_id');
            $table->unsignedBigInteger('distrito_id');
            $table->unsignedBigInteger('ciudad_id');
            $table->string('telefono');
            $table->string('email');
            $table->timestamps();

            // Foreign keys
            $table->foreign('documento_id')->references('id')->on('documents');
            $table->foreign('departamento_id')->references('id')->on('departments');
            $table->foreign('provincia_id')->references('id')->on('provinces');
            $table->foreign('distrito_id')->references('id')->on('districts');
            $table->foreign('ciudad_id')->references('id')->on('cities');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}

