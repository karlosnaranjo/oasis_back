<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Evolutions;
use App\Models\ViewEvolutions;
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
		$query = ViewEvolutions::query();
		return $this->indexGrid($request, $query);
	}

	public function getEvolutionsReport($id)
	{
		$query =  Evolutions::select(
			'evolutions.id',
			'patient.code as patient_code',
			'patient.name as patient_name',
			'evolutions.date_of_evolution',
			'evolutions.comments',
			'evolutions.area',
			'employee.name as employee_name',
		)
			->leftJoin('patients as patient', function ($join) {
				$join->on('patient.id', '=', 'evolutions.patient_id')
					->whereNull('patient.deleted_at');
			})
			->leftJoin('employees as employee', function ($join) {
				$join->on('employee.id', '=', 'evolutions.employee_id')
					->whereNull('employee.deleted_at');
			})
			->where('patient_id', $id)
			->where('evolutions.status', 1)
			->whereNull('evolutions.deleted_at')
			->get();
		return response([
			'evolution' => $query
		], 200);
	}

	public static function lastCode()
	{
		$lastEvolution = Evolutions::orderBy("id", "DESC")->first();
		$consecutive = isset($lastEvolution) ? $lastEvolution->codigo : 0;
		$consecutive = (int)$consecutive + 1;
		$consecutive = str_repeat('0', 5 - strlen($consecutive)) . $consecutive;
		return  $consecutive;
	}
}
