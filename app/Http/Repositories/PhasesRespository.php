<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Phases;

use App\Traits\IndexTrait;

/**
 * Class PhasesRespository
 *
 */
class PhasesRespository
{
    use IndexTrait;
    public function getPhasesList(Request $request)
    {
        $query =  Phases::select(
            'phases.id',
            'phases.code',
            'phases.name',
            'phases.status',

        );
        return $this->indexGrid($request, $query);
    }

    public static function lastCode()
    {
        $lastPhase = Phases::orderBy("id", "DESC")->first();
        $consecutive = isset($lastPhase) ? $lastPhase->code : 0;
        $consecutive = (int)$consecutive + 1;
        $consecutive = str_repeat('0', 5 - strlen($consecutive)) . $consecutive;
        return  $consecutive;
    }
}
