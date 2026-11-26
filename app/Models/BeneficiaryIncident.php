<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryIncident extends Model{

    protected $table = 'benefiter_occurrences';

    protected $fillable = [
        'description',
        'occurrence_date',
        'benefiter_id',
        'user_id'];

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}

