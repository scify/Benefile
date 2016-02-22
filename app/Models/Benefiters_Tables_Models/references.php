<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'benefiter_referrals';

    protected $fillable = [
        'description',
        'reference_date',
        'benefiter_id',
        'reference_lookup_id'];
}