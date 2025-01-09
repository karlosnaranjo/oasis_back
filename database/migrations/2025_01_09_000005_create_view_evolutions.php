<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW view_evolutions AS
            SELECT 
                evolutions.id,
                patient.code AS patient_code,
                patient.name AS patient_name,
                evolutions.date_of_evolution,
                evolutions.comments,
                evolutions.area,
                employee.name AS employee_name
            FROM 
                evolutions
            LEFT JOIN 
                patients AS patient ON patient.id = evolutions.patient_id AND patient.deleted_at IS NULL
            LEFT JOIN 
                employees AS employee ON employee.id = evolutions.employee_id AND employee.deleted_at IS NULL
            WHERE 
                evolutions.status = 1
                AND evolutions.deleted_at IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS view_evolutions');
    }
};
