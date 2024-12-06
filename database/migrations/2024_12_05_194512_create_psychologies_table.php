<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('psychologies', function (Blueprint $table) {
            $table->id();
            $table->string('code',20)->nullable()->comment('Codigo');
            $table->dateTime('issue_date')->nullable()->comment('Fecha de Elaboracion');
            $table->unsignedBigInteger('patient_id')->nullable()->comment('Paciente');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->text('reason_of_visit')->nullable()->comment('Motivo de consulta');
            $table->text('family_history')->nullable()->comment('Antecedentes familiares');
            $table->text('work_history')->nullable()->comment('Antecedentes laborales');
            $table->text('personal_history')->nullable()->comment('Historia personal');
            $table->text('addiction_history')->nullable()->comment('Historia de adiccion');
            $table->text('way_administration')->nullable()->comment('Via de administracion');
            $table->text('other_substances')->nullable()->comment('Otras subtancias');
            $table->text('highest_substance')->nullable()->comment('Mayor sustancia');
            $table->enum('current_consumption',['SI', 'NO'])->nullable()->comment('Consumo actual');
            $table->enum('addictive_behavior',['SI', 'NO'])->nullable()->comment('Esta realizando la conducta adictiva?');
            $table->enum('previous_treatment',['SI', 'NO'])->nullable()->comment('Tratamientos anteriores');
            $table->text('place_treatment')->nullable()->comment('Lugares y tiempos de tratamiento');
            $table->text('mental_illness')->nullable()->comment('Historia de enfermedad mental');
            $table->text('suicidal_thinking')->nullable()->comment('Ha tenido pensamientos o intentos de suicidio?');
            $table->text('homicidal_attempts')->nullable()->comment('Ha tenido pensamientos o intentos homicidas?');
            $table->text('language')->nullable()->comment('Lenguaje y pensamiento');
            $table->text('orientation')->nullable()->comment('Orientacion (Persona, espacio y tiempo):');
            $table->text('memory')->nullable()->comment('Memoria');
            $table->text('mood')->nullable()->comment('Estado de animo');
            $table->text('feeding')->nullable()->comment('Alimentacion');
            $table->text('sleep')->nullable()->comment('Sueno');
            $table->text('medication')->nullable()->comment('Esta tomando algun tipo de medicamento?');
            $table->text('legal_issues')->nullable()->comment('Problematicas judiciales y/o comportamentales');
            $table->text('defense_mechanism')->nullable()->comment('Mecanismos de defensa');
            $table->text('another_difficulty')->nullable()->comment('Otras dificultades');
            $table->text('expectation')->nullable()->comment('Que expectativas y motivaciones tiene para el proceso?');
            $table->text('diagnostic_impression')->nullable()->comment('Impresion diagnostica');
            $table->text('intervention')->nullable()->comment('Propuesta de intervencion');
            $table->text('comments')->nullable()->comment('Observaciones');
            $table->unsignedBigInteger('employee_id')->nullable()->comment('Funcionario');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->boolean('status')->nullable()->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('psychologies');
    }
};