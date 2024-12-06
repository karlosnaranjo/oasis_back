<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;

use Illuminate\Database\Eloquent\Relations\HasOne;


class Targets extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected $table = 'targets';

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
		'name',
		'phase_id',
		'status',

    ];

	public function phase(): HasOne
    {
        return $this->hasOne(Phases::class , 'phase_id');
    }


}
