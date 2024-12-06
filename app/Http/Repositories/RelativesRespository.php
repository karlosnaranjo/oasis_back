<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Relatives;

use App\Traits\IndexTrait;

/**
 * Class RelativesRespository
 *
 */
class RelativesRespository
{
    use IndexTrait;
    public function getRelativesList(Request $request)
    {
        $query =  Relatives::select(
			'relatives.id',
			'relatives.code',
			'relatives.name',
			'relatives.status',

            )
;
        return $this->indexGrid($request, $query);
    }
}
