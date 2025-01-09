<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Evaluations;
use App\Models\ViewEvaluations;
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
		$query = ViewEvaluations::query();
		return $this->indexGrid($request, $query);
	}

	public static function lastCode()
	{
		$lastEvaluation = Evaluations::orderBy("id", "DESC")->first();
		$consecutive = isset($lastEvaluation) ? $lastEvaluation->code : 0;
		$consecutive = (int)$consecutive + 1;
		$consecutive = str_repeat('0', 5 - strlen($consecutive)) . $consecutive;
		return  $consecutive;
	}
}
