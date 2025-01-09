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
            CREATE VIEW view_psychologies AS
            SELECT 
                psychologies.id,
                patient.image AS image,
                psychologies.code,
                psychologies.issue_date,
                patient.name AS patient_name,
                psychologies.reason_of_visit,
                psychologies.family_history,
                psychologies.work_history,
                psychologies.personal_history,
                psychologies.addiction_history,
                psychologies.way_administration,
                psychologies.other_substances,
                psychologies.highest_substance,
                psychologies.current_consumption,
                psychologies.addictive_behavior,
                psychologies.previous_treatment,
                psychologies.place_treatment,
                psychologies.mental_illness,
                psychologies.suicidal_thinking,
                psychologies.homicidal_attempts,
                psychologies.language,
                psychologies.orientation,
                psychologies.memory,
                psychologies.mood,
                psychologies.feeding,
                psychologies.sleep,
                psychologies.medication,
                psychologies.legal_issues,
                psychologies.defense_mechanism,
                psychologies.another_difficulty,
                psychologies.expectation,
                psychologies.diagnostic_impression,
                psychologies.intervention,
                psychologies.comments,
                employee.name AS employee_name,
                psychologies.status
            FROM 
                psychologies
            LEFT JOIN 
                patients AS patient ON patient.id = psychologies.patient_id AND patient.deleted_at IS NULL
            LEFT JOIN 
                employees AS employee ON employee.id = psychologies.employee_id AND employee.deleted_at IS NULL
            ORDER BY 
                psychologies.id DESC
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS view_psychologies');
    }
};
