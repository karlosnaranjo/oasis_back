<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\PsychologyDrugs;

use App\Traits\IndexTrait;

/**
 * Class PsychologyDrugsRespository
 *
 */
class PsychologyDrugsRespository
{
    use IndexTrait;
    public function getPsychologyDrugsList(Request $request)
    {
        $query =  PsychologyDrugs::select(
			'psychology_drugs.id',
			'drug.name as drug_name',
			'psychology_drugs.start_age',
			'psychology_drugs.frecuency_of_consumption',
			'psychology_drugs.maximum_abstinence',
			'psychology_drugs.consumption_date',

            )
			->leftJoin('drugs as drug', function($join) {
				$join->on('drug.id', '=', 'psychology_drugs.drug_id')
					->whereNull('drug.deleted_at');
			});
        return $this->indexGrid($request, $query);
    }
}
