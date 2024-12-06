<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('employees', function (Blueprint $table) {
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
            $table->string('job_title',50)->nullable()->comment('Cargo');
            $table->boolean('status')->nullable()->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};