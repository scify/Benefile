<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

/**
 * The CSV exports of the administrators: all the beneficiaries, and the history of the work on their files.
 */
class BeneficiaryCsvExporter
{
    public function __construct(
        private readonly FolderHistoryService $history,
        private readonly DatesHelper $dates,
    ) {
    }

    /**
     * All the beneficiaries, one row each, with the date of the last work on their file.
     */
    public function beneficiaries(?User $viewer): string
    {
        $titles = ['folder_number', 'lastname', 'name', 'gender', 'birth_date', 'fathers_name', 'mothers_name',
            'nationality_country', 'origin_country', 'ethnic_group', 'arrival_date', 'telephone', 'address',
            'relatives_residence', 'number_of_children', 'children_names', 'translator_needed_caps', 'work_caps',
            'abandon_reason_caps', 'travel_route_caps', 'travel_duration_caps', 'detention_duration_caps',
            'ethnic_group_caps', 'social_history_caps', 'marital_status_caps', 'education_caps', 'legal_work_caps',
            'kind_of_work_caps', 'registration_date_caps', 'last_update_caps'];
        $rows = [array_map(fn (string $title) => Lang::get('basic_info_form.'.$title), $titles)];
        $date = fn ($value) => $this->dates->getFinelyFormattedStringDateFromDBDate($value);
        $history = $this->history->forAllBeneficiaries($viewer);

        foreach ($this->beneficiaryRows() as $benefiter) {
            $latest = $history[$benefiter->id][0] ?? null;
            $rows[] = [
                $benefiter->folder_number, $benefiter->lastname, $benefiter->name, $benefiter->gender,
                $date($benefiter->birth_date), $benefiter->fathers_name, $benefiter->mothers_name,
                $benefiter->nationality_country, $benefiter->origin_country, $benefiter->ethnic_group,
                $date($benefiter->arrival_date), $benefiter->telephone, $benefiter->address,
                $benefiter->relatives_residence, $benefiter->number_of_children, $benefiter->children_names,
                $this->yesOrNo($benefiter->language_interpreter_needed),
                $this->yesOrNo($benefiter->is_benefiter_working),
                $benefiter->country_abandon_reason, $benefiter->travel_route, $benefiter->travel_duration,
                $date($benefiter->detention_date), $benefiter->ethnic_group, $benefiter->social_history,
                $benefiter->marital_status_title, $benefiter->education_title, $benefiter->legal_working_status,
                $benefiter->work_title, $date($benefiter->created_at),
                $latest !== null ? $date($latest->getDate()) : '',
            ];
        }

        return $this->toCsv($rows);
    }

    /**
     * The history of the work on all the files, one row per piece of work.
     */
    public function folderHistory(?User $viewer): string
    {
        $p = 'all_folders_history_csv_titles.';
        $rows = [[
            Lang::get($p.'folder_number'), Lang::get($p.'benefiter_name'), Lang::get($p.'user_name'),
            Lang::get($p.'speciality'), Lang::get($p.'location'), Lang::get($p.'date'),
            Lang::get($p.'comments'), Lang::get($p.'folder_name'),
        ]];
        foreach ($this->history->forAllBeneficiaries($viewer) as $entries) {
            foreach ($entries as $row) {
                $rows[] = [
                    $row->getFolderNumber(), $row->getBenefiterName(), $row->getUserName(),
                    $row->getSpeciality(), $row->getLocation(),
                    $this->dates->getFinelyFormattedStringDateFromDBDate($row->getDate()),
                    $row->getComments(), Lang::get('basic_info_form.'.$row->getFolderName()),
                ];
            }
        }

        return $this->toCsv($rows);
    }

    /**
     * @return array<int, object>
     */
    private function beneficiaryRows(): array
    {
        return DB::select('select b.*, gl.gender, msl.marital_status_title, el.education_title, wll.description '.
            'as legal_working_status, wtll.work_title, '.
            'carl.description as country_abandon_reason from benefiters as b '.
            'left join benefiters_legal_status as bls on b.id = bls.benefiter_id left join genders_lookup as gl '.
            'on b.gender_id = gl.id left join marital_status_lookup as msl on b.marital_status_id = msl.id '.
            'left join education_lookup as el on b.education_id = el.id left join working_legally_lookup as wll '.
            'on b.working_legally = wll.id left join work_title_list_lookup as wtll on b.work_title_id = wtll.id '.
            'left join country_abandon_reasons_lookup as carl on b.country_abandon_reason_id = carl.id '.
            'where b.deleted_at is null group by b.id order by b.id');
    }

    private function yesOrNo(mixed $binary): ?string
    {
        return $binary === null || $binary === '' ? null : Lang::get('search/search.binary_'.$binary);
    }

    /**
     * Writes rows as CSV text.
     *
     * Quotes inside values are escaped. A value that starts with = + - @ or a
     * tab gets a leading apostrophe, so that a spreadsheet shows it as text and
     * does not run it as a formula ("CSV injection").
     *
     * @param  list<array<int, mixed>>  $rows
     */
    private function toCsv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($handle, array_map(function ($cell): string {
                $cell = (string) $cell;

                return preg_match('/^[=+\-@\t\r]/', $cell) ? "'".$cell : $cell;
            }, $row), ',', '"', '');
        }
        rewind($handle);
        $csv = (string) stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
