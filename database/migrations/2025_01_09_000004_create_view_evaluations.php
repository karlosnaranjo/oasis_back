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
            CREATE VIEW view_evaluations AS
            SELECT 
                evaluations.id,
                patient.image AS image,
                evaluations.code,
                patient.name AS patient_name,
                evaluations.creation_date,
                phase.name AS phase_name,
                target.name AS target_name,
                evaluations.start_date,
                evaluations.end_date,
                evaluations.clinical_team,
                evaluations.achievement,
                evaluations.strategy,
                evaluations.requirement,
                evaluations.test,
                evaluations.status
            FROM 
                evaluations
            LEFT JOIN 
                patients AS patient ON patient.id = evaluations.patient_id AND patient.deleted_at IS NULL
            LEFT JOIN 
                phases AS phase ON phase.id = evaluations.phase_id AND phase.deleted_at IS NULL
            LEFT JOIN 
                targets AS target ON target.id = evaluations.target_id AND target.deleted_at IS NULL
            ORDER BY 
                evaluations.id DESC
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS view_evaluations');
    }
};
