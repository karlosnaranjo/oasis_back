<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Targets;

use App\Traits\IndexTrait;

/**
 * Class TargetsRespository
 *
 */
class TargetsRespository
{
    use IndexTrait;
    public function getTargetsList(Request $request)
    {
        $query =  Targets::select(
			'targets.id',
			'targets.code',
			'targets.name',
			'phase.name as phase_name',
			'targets.status',

            )
			->leftJoin('phases as phase', function($join) {
				$join->on('phase.id', '=', 'targets.phase_id')
					->whereNull('phase.deleted_at');
			});
        return $this->indexGrid($request, $query);
    }
}
