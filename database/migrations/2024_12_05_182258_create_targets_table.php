<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->string('code',20)->nullable()->comment('Codigo');
            $table->string('name',50)->nullable()->comment('Nombre fase');
            $table->unsignedBigInteger('phase_id')->nullable()->comment('Fase');
            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade');
            $table->boolean('status')->nullable()->comment('Estado');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('targets');
    }
};