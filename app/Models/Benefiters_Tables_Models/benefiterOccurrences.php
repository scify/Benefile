<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class benefiterOccurrences extends Model{

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

