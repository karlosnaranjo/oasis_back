<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\PsychologyRelatives;

use App\Traits\IndexTrait;

/**
 * Class PsychologyRelativesRespository
 *
 */
class PsychologyRelativesRespository
{
    use IndexTrait;
    public function getPsychologyRelativesList(Request $request)
    {
        $query =  PsychologyRelatives::select(
			'psychology_relatives.id',
			'psychology_relatives.name',
			'relative.name as relative_name',
			'psychology_relatives.age',
			'psychology_relatives.relationship_type',

            )
			->leftJoin('relatives as relative', function($join) {
				$join->on('relative.id', '=', 'psychology_relatives.relative_id')
					->whereNull('relative.deleted_at');
			});
        return $this->indexGrid($request, $query);
    }
}
