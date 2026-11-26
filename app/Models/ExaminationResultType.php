<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationResultType extends Model
{
    protected $table = 'medical_examination_results_lookup';
    protected $primaryKey = 'id';
    protected $fillable = ['description'];
}
