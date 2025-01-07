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

        );
        return $this->indexGrid($request, $query);
    }

    public static function lastCode()
    {
        $lastDrug = Drugs::orderBy("id", "DESC")->first();
        $consecutive = isset($lastDrug) ? $lastDrug->code : 0;
        $consecutive = (int)$consecutive + 1;
        $consecutive = str_repeat('0', 5 - strlen($consecutive)) . $consecutive;
        return  $consecutive;
    }
}
