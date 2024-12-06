<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('code',20)->nullable()->comment('Codigo');
            $table->unsignedBigInteger('patient_id')->nullable()->comment('Paciente');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->dateTime('creation_date')->nullable()->comment('Fecha de Creacion');
            $table->unsignedBigInteger('phase_id')->nullable()->comment('Fase');
            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade');
            $table->unsignedBigInteger('target_id')->nullable()->comment('Objetivo');
            $table->foreign('target_id')->references('id')->on('targets')->onDelete('cascade');
            $table->dateTime('start_date')->nullable()->comment('Fecha inicio');
            $table->dateTime('end_date')->nullable()->comment('Fecha final');
            $table->text('clinical_team')->nullable()->comment('Apreciacion Equipo Clinico');
            $table->text('achievement')->nullable()->comment('Logros y Dificultades');
            $table->text('strategy')->nullable()->comment('Estrategias Utilizadas');
            $table->text('requirement')->nullable()->comment('Exigencias');
            $table->enum('test',['Positivo', 'Negativo', 'Observacion'])->nullable()->comment('Evaluacion');
            $table->boolean('status')->nullable()->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};