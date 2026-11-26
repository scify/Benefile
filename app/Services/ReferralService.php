<?php

namespace App\Services;

use App\Models\Benefiters_Tables_Models\BenefiterReferrals;
use App\Models\Benefiters_Tables_Models\BenefiterReferrals_lookup;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;

/**
 * The referrals of a beneficiary to other services, in the basic information.
 */
class ReferralService
{
    public function __construct(private readonly DatesHelper $dates, private readonly Guard $auth)
    {
    }

    /**
     * Saves the referrals of the form for this beneficiary, as the logged-in user.
     *
     * @param  array<string, mixed>|\ArrayAccess<string, mixed>  $request
     */
    public function add(int $benefiterId, $request): void
    {
        $types = (array) ($request['basic_info_referrals_id'] ?? []);
        foreach (array_values($types) as $i => $type) {
            $referral = new BenefiterReferrals();
            $referral->description = $request['basic_info_referrals_text'][$i] ?? '';
            $referral->referral_date = $this->dates->makeDBFriendlyDate($request['basic_info_referrals_date'][$i] ?? '');
            $referral->benefiter_id = $benefiterId;
            $referral->referral_lookup_id = $type;
            $referral->user_id = $this->auth->id();
            $referral->save();
        }
    }

    /**
     * Deletes a referral. Only a referral of this beneficiary is deleted.
     */
    public function delete(int $benefiterId, int $referralId): void
    {
        BenefiterReferrals::where('id', $referralId)->where('benefiter_id', $benefiterId)->delete();
    }

    /**
     * @return Collection<int, BenefiterReferrals> The latest first.
     */
    public function forBeneficiary(int $benefiterId): Collection
    {
        return BenefiterReferrals::where('benefiter_id', $benefiterId)->orderBy('referral_date', 'desc')->get();
    }

    /**
     * @return list<BenefiterReferrals_lookup> The types of referral.
     */
    public function types(): array
    {
        return BenefiterReferrals_lookup::get()->all();
    }
}
