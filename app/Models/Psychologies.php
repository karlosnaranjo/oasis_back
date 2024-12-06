<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ScopesTrait;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Psychologies extends Model
{
    use HasFactory, SoftDeletes, ScopesTrait;
    /**
     * Database table name.
     */
    protected $table = 'psychologies';

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
		'issue_date',
		'patient_id',
		'reason_of_visit',
		'family_history',
		'work_history',
		'personal_history',
		'addiction_history',
		'way_administration',
		'other_substances',
		'highest_substance',
		'current_consumption',
		'addictive_behavior',
		'previous_treatment',
		'place_treatment',
		'mental_illness',
		'suicidal_thinking',
		'homicidal_attempts',
		'language',
		'orientation',
		'memory',
		'mood',
		'feeding',
		'sleep',
		'medication',
		'legal_issues',
		'defense_mechanism',
		'another_difficulty',
		'expectation',
		'diagnostic_impression',
		'intervention',
		'comments',
		'employee_id',
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
	public function psychologyRelatives(): HasMany
    {
        return $this->hasMany(PsychologyRelatives::class );
    }
	public function psychologyDrugs(): HasMany
    {
        return $this->hasMany(PsychologyDrugs::class );
    }


}
