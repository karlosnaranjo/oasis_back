<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\ViewPatients;
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
		$query = ViewPatients::query();
		return $this->indexGrid($request, $query);
	}
}
