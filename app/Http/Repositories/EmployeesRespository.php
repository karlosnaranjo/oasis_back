<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;

use App\Models\Employees;

use App\Traits\IndexTrait;

/**
 * Class EmployeesRespository
 *
 */
class EmployeesRespository
{
    use IndexTrait;
    public function getEmployeesList(Request $request)
    {
        $query =  Employees::select(
			'employees.id',
			'employees.document_type',
			'employees.code',
			'employees.name',
			'employees.image',
			'employees.gender',
			'employees.marital_status',
			'employees.date_of_birth',
			'employees.address1',
			'employees.address2',
			'employees.phone',
			'employees.cellphone',
			'employees.email',
			'employees.job_title',
			'employees.status',

            )
;
        return $this->indexGrid($request, $query);
    }
}
