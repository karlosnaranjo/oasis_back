<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;

use Illuminate\Database\Eloquent\Relations\HasOne;


class Evolutions extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected $table = 'evolutions';

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
		'code',
		'patient_id',
		'employee_id',
		'date_of_evolution',
		'area',
		'comments',
		'status',

    ];

	public function patient(): HasOne
    {
        return $this->hasOne(Patients::class , 'patient_id');
    }
	public function employee(): HasOne
    {
        return $this->hasOne(Employees::class , 'employee_id');
    }


}
