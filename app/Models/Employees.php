<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;



class Employees extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected $table = 'employees';

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
		'status',

    ];



}
