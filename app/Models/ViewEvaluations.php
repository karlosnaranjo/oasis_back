<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;




class ViewEvaluations extends Model
{
    protected $table = 'view_evaluations';
    // Indica que no hay timestamps
    public $timestamps = false;
    // Desactiva la protección de clave primaria
    protected $primaryKey = null;
    public $incrementing = false;
}
