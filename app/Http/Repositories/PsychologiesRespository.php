<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Psychologies;
use App\Models\ViewPsychologies;
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
		$query = ViewPsychologies::query();
		return $this->indexGrid($request, $query);
	}

	public static function lastCode()
	{
		$lastpsychology = Psychologies::orderBy("id", "DESC")->first();
		$consecutive = isset($lastpsychology) ? $lastpsychology->code : 0;
		$consecutive = (int)$consecutive + 1;
		$consecutive = str_repeat('0', 5 - strlen($consecutive)) . $consecutive;
		return  $consecutive;
	}
}
