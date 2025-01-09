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
            CREATE VIEW view_patients AS
            SELECT 
                patients.id,
                patients.document_type,
                patients.code,
                patients.name,
                patients.image,
                patients.gender,
                patients.marital_status,
                patients.date_of_birth,
                patients.address1,
                patients.address2,
                patients.phone,
                patients.cellphone,
                patients.email,
                patients.job_title,
                patients.health_insurance,
                patients.level_of_education,
                patients.admission_date,
                patients.second_date,
                patients.third_date,
                patients.responsible_adult,
                patients.responsible_adult_code,
                patients.relationship,
                patients.responsible_adult_phone,
                patients.responsible_adult_cellphone,
                drug.name AS drug_name,
                patients.orientation,
                patients.body_language,
                patients.ideation,
                patients.delusions,
                patients.hallucinations,
                patients.eating_problems,
                patients.treatment_motivations,
                patients.end_date,
                patients.cause_of_end,
                patients.end_date_second,
                patients.cause_of_end_second,
                patients.end_date_third,
                patients.cause_of_end_third,
                patients.comments,
                employee.name AS employee_name,
                patients.status
            FROM 
                patients
            LEFT JOIN 
                drugs AS drug ON drug.id = patients.drug_id AND drug.deleted_at IS NULL
            LEFT JOIN 
                employees AS employee ON employee.id = patients.employee_id AND employee.deleted_at IS NULL
            ORDER BY 
                patients.id DESC
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS view_patients');
    }
};
