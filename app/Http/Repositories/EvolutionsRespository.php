<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Evolutions;

use App\Traits\IndexTrait;

/**
 * Class EvolutionsRespository
 *
 */
class EvolutionsRespository
{
    use IndexTrait;
    public function getEvolutionsList(Request $request)
    {
        $query =  Evolutions::select(
			'evolutions.id',
			'evolutions.code',
			'patient.name as patient_name',
			'employee.name as employee_name',
			'evolutions.date_of_evolution',
			'evolutions.area',
			'evolutions.comments',
			'evolutions.status',

            )
			->leftJoin('patients as patient', function($join) {
				$join->on('patient.id', '=', 'evolutions.patient_id')
					->whereNull('patient.deleted_at');
			})
			->leftJoin('employees as employee', function($join) {
				$join->on('employee.id', '=', 'evolutions.employee_id')
					->whereNull('employee.deleted_at');
			});
        return $this->indexGrid($request, $query);
    }
}
