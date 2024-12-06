<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('psychology_relatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('psychology_id')->nullable()->comment('id');
            $table->foreign('psychology_id')->references('id')->on('psychologies')->onDelete('cascade');
            $table->string('name',50)->nullable()->comment('Nombres y Apellidos');
            $table->unsignedBigInteger('relative_id')->nullable()->comment('Parentesco');
            $table->string('age',20)->nullable()->comment('Edad');
            $table->enum('relationship_type',['Estrecha', 'Cercana', 'Distante'])->nullable()->comment('Tipo de relacion');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('psychology_relatives');
    }
};