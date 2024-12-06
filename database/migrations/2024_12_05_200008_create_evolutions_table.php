<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('evolutions', function (Blueprint $table) {
            $table->id();
            $table->string('code',20)->nullable()->comment('Codigo');
            $table->unsignedBigInteger('patient_id')->nullable()->comment('Paciente');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('employee_id')->nullable()->comment('Empleado');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->dateTime('date_of_evolution')->nullable()->comment('Fecha de registro');
            $table->string('area',20)->nullable()->comment('Area que registra evolucion');
            $table->text('comments')->nullable()->comment('Notas');
            $table->boolean('status')->nullable()->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('evolutions');
    }
};