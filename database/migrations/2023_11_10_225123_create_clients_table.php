<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('doc_identidad');
            $table->string('name');
            $table->string('direccion');
            $table->enum('tipo_persona',[01,02]);
            $table->enum('tipo_identidad',[1,4,6,7]);
            $table->string('canal');
            $table->string('subcanal');
            $table->string('vendedor');
            $table->string('number');
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
        Schema::dropIfExists('clients');
    }
}
