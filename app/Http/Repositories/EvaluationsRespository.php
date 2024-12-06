<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Evaluations;

use App\Traits\IndexTrait;

/**
 * Class EvaluationsRespository
 *
 */
class EvaluationsRespository
{
    use IndexTrait;
    public function getEvaluationsList(Request $request)
    {
        $query =  Evaluations::select(
			'evaluations.id',
			'evaluations.code',
			'patient.name as patient_name',
			'evaluations.creation_date',
			'phase.name as phase_name',
			'target.name as target_name',
			'evaluations.start_date',
			'evaluations.end_date',
			'evaluations.clinical_team',
			'evaluations.achievement',
			'evaluations.strategy',
			'evaluations.requirement',
			'evaluations.test',
			'evaluations.status',

            )
			->leftJoin('patients as patient', function($join) {
				$join->on('patient.id', '=', 'evaluations.patient_id')
					->whereNull('patient.deleted_at');
			})
			->leftJoin('phases as phase', function($join) {
				$join->on('phase.id', '=', 'evaluations.phase_id')
					->whereNull('phase.deleted_at');
			})
			->leftJoin('targets as target', function($join) {
				$join->on('target.id', '=', 'evaluations.target_id')
					->whereNull('target.deleted_at');
			});
        return $this->indexGrid($request, $query);
    }
}
