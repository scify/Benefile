<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'benefiter_referrals';

    protected $fillable = [
        'description',
        'referral_date',
        'benefiter_id',
        'user_id',
        'referral_lookup_id'];

    public function referralType(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\ReferralType::class, 'id', 'referral_lookup_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }
}
