<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->enum('document_type',['DNI', 'RUC', 'TI', 'CEDULA'])->nullable()->comment('Tipo de documento');
            $table->string('code',20)->nullable()->comment('Numero');
            $table->string('name',50)->nullable()->comment('Nombres');
            $table->string('image',255)->nullable()->comment('Imagen');
            $table->enum('gender',['Masculino', 'Femenino', 'No binario', 'Prefiero no especificar'])->nullable()->comment('Genero');
            $table->enum('marital_status',['Soltero(a)', 'Casado(a)', 'Union Libre', 'Separado(a)', 'Divorciado(a)', 'Viudo(a)'])->nullable()->comment('Estado Civil');
            $table->date('date_of_birth')->nullable()->comment('Fecha de Nacimiento');
            $table->string('address1',255)->nullable()->comment('Direccion 1');
            $table->string('address2',255)->nullable()->comment('Direccion 2');
            $table->string('phone',20)->nullable()->comment('Telefono');
            $table->string('cellphone',50)->nullable()->comment('Celular');
            $table->string('email',255)->nullable()->comment('E-Mail');
            $table->string('job_title',50)->nullable()->comment('Ocupacion');
            $table->string('health_insurance',50)->nullable()->comment('EPS');
            $table->string('level_of_education',50)->nullable()->comment('Escolaridad');
            $table->date('admission_date')->nullable()->comment('Fecha de Ingreso');
            $table->date('second_date')->nullable()->comment('Fecha de Ingreso (por segunda vez)');
            $table->date('third_date')->nullable()->comment('Fecha de Ingreso (por tercera vez)');
            $table->string('responsible_adult',50)->nullable()->comment('Acudiente');
            $table->string('responsible_adult_code',50)->nullable()->comment('Documento del acudiente');
            $table->string('relationship',50)->nullable()->comment('Parentesco');
            $table->string('responsible_adult_phone',50)->nullable()->comment('Telefono');
            $table->string('responsible_adult_cellphone',50)->nullable()->comment('Celular');
            $table->unsignedBigInteger('drug_id')->nullable()->comment('Droga de impacto');
            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');
            $table->text('orientation')->nullable()->comment('Ubicacion (Tiempo - Espacio - Persona)');
            $table->string('body_language',255)->nullable()->comment('Lenguaje corporal');
            $table->string('ideation',255)->nullable()->comment('Ideacion o intento suicida');
            $table->string('delusions',255)->nullable()->comment('Delirios');
            $table->string('hallucinations',255)->nullable()->comment('Alucinaciones');
            $table->string('eating_problems',255)->nullable()->comment('Problemas de alimentacion');
            $table->text('treatment_motivations')->nullable()->comment('Motivacion al tratamiento');
            $table->dateTime('end_date')->nullable()->comment('Fecha de Salida');
            $table->enum('cause_of_end',['Egreso', 'Abandono', 'Fuga', 'Remision', 'Expulsion'])->nullable()->comment('Causa de salida');
            $table->dateTime('end_date_second')->nullable()->comment('Fecha de Salida (Por segunda vez)');
            $table->enum('cause_of_end_second',['Egreso', 'Abandono', 'Fuga', 'Remision', 'Expulsion'])->nullable()->comment('Causa de salida (Por segunda vez)');
            $table->dateTime('end_date_third')->nullable()->comment('Fecha de Salida (Por tercera vez)');
            $table->enum('cause_of_end_third',['Egreso', 'Abandono', 'Fuga', 'Remision', 'Expulsion'])->nullable()->comment('Causa de salida (Por tercera vez)');
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
        Schema::dropIfExists('patients');
    }
};