<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('code',20)->nullable()->comment('Codigo');
            $table->string('name',50)->nullable()->comment('Nombre');
            $table->string('technical_name',50)->nullable()->comment('Nombre tecnico');
            $table->boolean('status')->nullable()->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('drugs');
    }
};