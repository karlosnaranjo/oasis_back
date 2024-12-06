<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;

use Illuminate\Database\Eloquent\Relations\HasOne;


class Patients extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected $table = 'patients';

     /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = true;

    /**
     * Indicates if the IDs are auto-incrementing.
     * @var bool
     */

    protected $primaryKey = 'id';

    /**
     * We define the fields of the table in the var $fillable directly.
     */
    protected $fillable = [
		'document_type',
		'code',
		'name',
		'image',
		'gender',
		'marital_status',
		'date_of_birth',
		'address1',
		'address2',
		'phone',
		'cellphone',
		'email',
		'job_title',
		'health_insurance',
		'level_of_education',
		'admission_date',
		'second_date',
		'third_date',
		'responsible_adult',
		'responsible_adult_code',
		'relationship',
		'responsible_adult_phone',
		'responsible_adult_cellphone',
		'drug_id',
		'orientation',
		'body_language',
		'ideation',
		'delusions',
		'hallucinations',
		'eating_problems',
		'treatment_motivations',
		'end_date',
		'cause_of_end',
		'end_date_second',
		'cause_of_end_second',
		'end_date_third',
		'cause_of_end_third',
		'comments',
		'employee_id',
		'status',

    ];

	public function drug(): HasOne
    {
        return $this->hasOne(Drugs::class , 'drug_id');
    }
	public function employee(): HasOne
    {
        return $this->hasOne(Employees::class , 'employee_id');
    }


}
