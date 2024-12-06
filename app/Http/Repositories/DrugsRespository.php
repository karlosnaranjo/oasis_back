<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Drugs;

use App\Traits\IndexTrait;

/**
 * Class DrugsRespository
 *
 */
class DrugsRespository
{
    use IndexTrait;
    public function getDrugsList(Request $request)
    {
        $query =  Drugs::select(
			'drugs.id',
			'drugs.code',
			'drugs.name',
			'drugs.technical_name',
			'drugs.status',

            )
;
        return $this->indexGrid($request, $query);
    }
}
