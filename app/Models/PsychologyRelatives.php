<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class PsychologyRelatives extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected $table = 'psychology_relatives';

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
		'psychology_id',
		'name',
		'relative_id',
		'age',
		'relationship_type',

    ];

	public function psychology(): BelongsTo
    {
        return $this->belongsTo(Psychologies::class , 'psychology_id');
    }
	public function relative(): HasOne
    {
        return $this->hasOne(Relatives::class , 'relative_id');
    }


}
