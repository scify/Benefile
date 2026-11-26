<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralType extends Model
{
    protected $table = 'benefiter_referrals_lookup';
    protected $primaryKey = 'id';
    protected $fillable = ['description'];

    public function benefiterReferrals(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(\App\Models\Referral::class);
    }
}
