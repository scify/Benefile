<?php

namespace App\Services;

use App\Models\BasicFolderHistory;
use App\Models\Benefiters_Tables_Models\Benefiter;
use App\Models\Benefiters_Tables_Models\medical_location_lookup;
use App\Models\Benefiters_Tables_Models\medical_visits;
use App\Models\LegalSession;
use App\Models\PsychosocialSession;
use App\Models\User;
use App\Models\ViewModels\AllFoldersUsageHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The history of the work on the files: who did what in which folder, where and when.
 *
 * Users and places are found by their id, never by their position in a list.
 * They are loaded once, so the history of all the beneficiaries needs a
 * few queries, not a few queries for each beneficiary.
 */
class FolderHistoryService
{
    /** @var Collection<int, User>|null */
    private ?Collection $users = null;

    /** @var Collection<int, string>|null */
    private ?Collection $locations = null;

    /**
     * The history of one beneficiary, the latest first.
     *
     * @param  User|null  $viewer  The user who sees the history: the notes of psychosocial sessions are only for psychologists and administrators.
     * @return list<AllFoldersUsageHistory>
     */
    public function forBeneficiary(int $benefiterId, ?User $viewer): array
    {
        $benefiter = Benefiter::find($benefiterId);
        if ($benefiter === null) {
            return [];
        }

        return $this->build(collect([$benefiter]), $viewer)[$benefiter->id] ?? [];
    }

    /**
     * The history of all the beneficiaries, each one the latest first.
     *
     * @return array<int, list<AllFoldersUsageHistory>> Beneficiary id => history.
     */
    public function forAllBeneficiaries(?User $viewer): array
    {
        return $this->build(Benefiter::orderBy('id')->get(), $viewer);
    }

    /**
     * @param  Collection<int, Benefiter>  $benefiters
     * @return array<int, list<AllFoldersUsageHistory>>
     */
    private function build(Collection $benefiters, ?User $viewer): array
    {
        $ids = $benefiters->pluck('id')->all();
        $seeNotes = $viewer !== null && $viewer->hasRole(User::ROLE_PSYCHOLOGIST);
        $history = array_fill_keys($ids, []);
        $byId = $benefiters->keyBy('id');

        foreach (BasicFolderHistory::whereIn('benefiter_id', $ids)->get() as $row) {
            $history[$row->benefiter_id][] = $this->entry($byId[$row->benefiter_id], (int) $row->user_id, (int) $row->medical_location_id, $row->update_date, (string) $row->comments, 'basic', false);
        }
        foreach (medical_visits::whereIn('benefiter_id', $ids)->get() as $row) {
            $history[$row->benefiter_id][] = $this->entry($byId[$row->benefiter_id], (int) $row->doctor_id, (int) $row->medical_location_id, $row->medical_visit_date, '', 'medical', true);
        }

        $legalFolders = \App\Models\Folders\LegalFolder::query()->whereIn('benefiter_id', $ids)->pluck('benefiter_id', 'id');
        foreach (LegalSession::whereIn('legal_folder_id', $legalFolders->keys())->get() as $row) {
            $benefiterId = $legalFolders[$row->legal_folder_id];
            $history[$benefiterId][] = $this->entry($byId[$benefiterId], (int) $row->user_id, (int) $row->medical_location_id, $row->legal_date, (string) $row->legal_comments, 'legal', false);
        }

        $socialFolders = \App\Models\Folders\SocialFolder::query()->whereIn('benefiter_id', $ids)->pluck('benefiter_id', 'id');
        foreach (PsychosocialSession::whereIn('social_folder_id', $socialFolders->keys())->get() as $row) {
            $benefiterId = $socialFolders[$row->social_folder_id];
            $history[$benefiterId][] = $this->entry($byId[$benefiterId], (int) $row->psychologist_id, (int) $row->medical_location_id, $row->session_date, $seeNotes ? (string) $row->session_comments : '', 'psychosocial', false);
        }

        foreach ($history as &$entries) {
            usort($entries, fn (AllFoldersUsageHistory $a, AllFoldersUsageHistory $b) => $b->getDate() <=> $a->getDate());
        }

        return $history;
    }

    private function entry(Benefiter $benefiter, int $userId, int $locationId, mixed $date, string $comments, string $folder, bool $withSpecialty): AllFoldersUsageHistory
    {
        $user = $this->users()->get($userId);
        $role = (string) ($user?->role->role ?? '');
        if ($withSpecialty && $user?->subrole !== null) {
            $role .= ' ('.$user->subrole->subrole.')';
        }

        return new AllFoldersUsageHistory(
            $benefiter->folder_number,
            $benefiter->name.' '.$benefiter->lastname,
            $user !== null ? $user->name.' '.$user->lastname : '',
            $role,
            $this->locations()->get($locationId, ''),
            new Carbon($date),
            $comments,
            $folder
        );
    }

    /** @return Collection<int, User> */
    private function users(): Collection
    {
        return $this->users ??= User::with('role', 'subrole')->get()->keyBy('id');
    }

    /** @return Collection<int, string> */
    private function locations(): Collection
    {
        return $this->locations ??= medical_location_lookup::pluck('description', 'id');
    }
}
