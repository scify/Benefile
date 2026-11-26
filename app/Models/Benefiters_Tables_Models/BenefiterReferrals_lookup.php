<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class BenefiterReferrals_lookup extends Model
{
    protected $table = 'benefiter_referrals_lookup';
    protected $primaryKey = 'id';
    protected $fillable = ['description'];

    public function benefiterReferrals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(\App\Models\Benefiters_Tables_Models\BenefiterReferrals::class);
    }
}
