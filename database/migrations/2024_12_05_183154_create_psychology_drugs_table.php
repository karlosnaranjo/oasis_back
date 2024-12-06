<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('psychology_drugs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('psychology_id')->nullable()->comment('id');
            $table->foreign('psychology_id')->references('id')->on('psychologies')->onDelete('cascade');
            $table->unsignedBigInteger('drug_id')->nullable()->comment('Sustancia');
            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->string('start_age',20)->nullable()->comment('Edad de inicio');
            $table->string('frecuency_of_consumption',50)->nullable()->comment('Frecuencia de Consumo');
            $table->smallInteger('maximum_abstinence')->nullable()->comment('Maxima abstinencia');
            $table->date('consumption_date')->nullable()->comment('Fecha ultimo consumo');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('psychology_drugs');
    }
};