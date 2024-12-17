<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Psychologies;

use App\Traits\IndexTrait;

/**
 * Class PsychologiesRespository
 *
 */
class PsychologiesRespository
{
	use IndexTrait;
	public function getPsychologiesList(Request $request)
	{
		$query =  Psychologies::select(
			'psychologies.id',
			'patient.image as image',
			'psychologies.code',
			'psychologies.issue_date',
			'patient.name as patient_name',
			'psychologies.reason_of_visit',
			'psychologies.family_history',
			'psychologies.work_history',
			'psychologies.personal_history',
			'psychologies.addiction_history',
			'psychologies.way_administration',
			'psychologies.other_substances',
			'psychologies.highest_substance',
			'psychologies.current_consumption',
			'psychologies.addictive_behavior',
			'psychologies.previous_treatment',
			'psychologies.place_treatment',
			'psychologies.mental_illness',
			'psychologies.suicidal_thinking',
			'psychologies.homicidal_attempts',
			'psychologies.language',
			'psychologies.orientation',
			'psychologies.memory',
			'psychologies.mood',
			'psychologies.feeding',
			'psychologies.sleep',
			'psychologies.medication',
			'psychologies.legal_issues',
			'psychologies.defense_mechanism',
			'psychologies.another_difficulty',
			'psychologies.expectation',
			'psychologies.diagnostic_impression',
			'psychologies.intervention',
			'psychologies.comments',
			'employee.name as employee_name',
			'psychologies.status',

		)
			->leftJoin('patients as patient', function ($join) {
				$join->on('patient.id', '=', 'psychologies.patient_id')
					->whereNull('patient.deleted_at');
			})
			->leftJoin('employees as employee', function ($join) {
				$join->on('employee.id', '=', 'psychologies.employee_id')
					->whereNull('employee.deleted_at');
			});
		return $this->indexGrid($request, $query);
	}
}
