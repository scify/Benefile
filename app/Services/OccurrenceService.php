<?php

namespace App\Services;

use App\Models\Benefiters_Tables_Models\benefiterOccurrences;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;

/**
 * The incidents in the basic information of a beneficiary.
 */
class OccurrenceService
{
    public function __construct(private readonly DatesHelper $dates, private readonly Guard $auth)
    {
    }

    /**
     * @param  array<string, mixed>|\ArrayAccess<string, mixed>  $request
     */
    public function add(int $benefiterId, $request): void
    {
        $incident = new benefiterOccurrences();
        $incident->description = $request['occurrences_comments'] ?? '';
        $incident->occurrence_date = $this->dates->makeDBFriendlyDate($request['occurrence_date'] ?? '');
        $incident->benefiter_id = $benefiterId;
        $incident->user_id = $this->auth->id();
        $incident->save();
    }

    /**
     * Changes an incident. Only an incident of this beneficiary changes.
     *
     * @param  array<string, mixed>|\ArrayAccess<string, mixed>  $request
     */
    public function update(int $benefiterId, int $incidentId, $request): void
    {
        $incident = benefiterOccurrences::where('id', $incidentId)->where('benefiter_id', $benefiterId)->first();
        if ($incident === null) {
            return;
        }
        $incident->occurrence_date = $this->dates->makeDBFriendlyDate($request['edited_occurrence_date'] ?? '');
        $incident->description = $request['edited_occurrences_comments'] ?? '';
        $incident->save();
    }

    /**
     * Deletes an incident. Only an incident of this beneficiary is deleted.
     */
    public function delete(int $benefiterId, int $incidentId): void
    {
        benefiterOccurrences::where('id', $incidentId)->where('benefiter_id', $benefiterId)->delete();
    }

    /**
     * @return Collection<int, benefiterOccurrences>
     */
    public function forBeneficiary(int $benefiterId): Collection
    {
        return benefiterOccurrences::where('benefiter_id', $benefiterId)->get();
    }
}
