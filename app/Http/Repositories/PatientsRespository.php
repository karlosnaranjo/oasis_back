<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Patients;

use App\Traits\IndexTrait;

/**
 * Class PatientsRespository
 *
 */
class PatientsRespository
{
	use IndexTrait;
	public function getPatientsList(Request $request)
	{
		$query =  Patients::select(
			'patients.id',
			'patients.document_type',
			'patients.code',
			'patients.name',
			'patients.image',
			'patients.gender',
			'patients.marital_status',
			'patients.date_of_birth',
			'patients.address1',
			'patients.address2',
			'patients.phone',
			'patients.cellphone',
			'patients.email',
			'patients.job_title',
			'patients.health_insurance',
			'patients.level_of_education',
			'patients.admission_date',
			'patients.second_date',
			'patients.third_date',
			'patients.responsible_adult',
			'patients.responsible_adult_code',
			'patients.relationship',
			'patients.responsible_adult_phone',
			'patients.responsible_adult_cellphone',
			'drug.name as drug_name',
			'patients.orientation',
			'patients.body_language',
			'patients.ideation',
			'patients.delusions',
			'patients.hallucinations',
			'patients.eating_problems',
			'patients.treatment_motivations',
			'patients.end_date',
			'patients.cause_of_end',
			'patients.end_date_second',
			'patients.cause_of_end_second',
			'patients.end_date_third',
			'patients.cause_of_end_third',
			'patients.comments',
			'employee.name as employee_name',
			'patients.status',

		)
			->leftJoin('drugs as drug', function ($join) {
				$join->on('drug.id', '=', 'patients.drug_id')
					->whereNull('drug.deleted_at');
			})
			->leftJoin('employees as employee', function ($join) {
				$join->on('employee.id', '=', 'patients.employee_id')
					->whereNull('employee.deleted_at');
			})
			->orderby('patients.id', 'desc');
		return $this->indexGrid($request, $query);
	}
}
