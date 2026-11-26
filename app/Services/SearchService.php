<?php namespace App\Services;

use Illuminate\Support\Facades\Log;

class SearchService{
    public function __construct(private readonly DatesHelper $datesHelper)
    {
    }


    /**
     * Searches the beneficiaries with the criteria of the search form.
     *
     * Every value of the form reaches the database as a bound parameter, never
     * as part of the SQL text. An empty field is not a criterion.
     *
     * @param  array<string, mixed>|\ArrayAccess<string, mixed>  $request
     * @return array<int, object>|null Null when the form has no criteria.
     */
    public function searchBenefiters($request){
        $value = fn (string $key): string => trim((string) ($request[$key] ?? ''));
        $id = fn (string $key): int => (int) ($request[$key] ?? 0);

        // Only the columns of the results table: the page gets them as JSON.
        $columns = 'b.id, b.folder_number, b.name, b.lastname, b.telephone, b.fathers_name, b.mothers_name, b.birth_date, '
            .'b.arrival_date, b.address, b.number_of_children, b.relatives_residence, b.language_interpreter_needed, '
            .'b.is_benefiter_working, b.travel_route, b.travel_duration, b.detention_date, b.origin_country, '
            .'b.nationality_country, b.ethnic_group, b.social_history, carl.description as country_abandon_reason';
        $select = "select $columns, gl.gender, msl.marital_status_title, el.education_title, wll.description as legal_working_status, wtll.work_title, floor(datediff(current_date, str_to_date(b.birth_date, '%Y-%m-%d'))/365) as age_in_years, count(mv.id) as incidents_counter, date(b.created_at) as created_at_date from benefiters as b left join benefiters_legal_status as bls on b.id = bls.benefiter_id left join medical_visits as mv on b.id = mv.benefiter_id left join medical_examination_results as mer on mv.id = mer.medical_visit_id left join medical_medication as mm on mv.id = mm.medical_visit_id left join genders_lookup as gl on b.gender_id = gl.id left join marital_status_lookup as msl on b.marital_status_id = msl.id left join education_lookup as el on b.education_id = el.id left join working_legally_lookup as wll on b.working_legally = wll.id left join work_title_list_lookup as wtll on b.work_title_id = wtll.id left join country_abandon_reasons_lookup as carl on b.country_abandon_reason_id = carl.id";
        $groupBy = " and b.deleted_at is null group by b.id";

        if (!empty($request['all_required'])) {
            Log::info("Returning all benefiters from DB.");
            return \DB::select($select . " where 1=1" . $groupBy);
        }

        $datesHelper = $this->datesHelper;
        $dbDate = fn (string $date): string => $datesHelper->makeDBSearchFriendlyDate($datesHelper->makeDBFriendlyDate($date));
        $where = [];
        $bindings = [];
        $like = fn (string $text): string => '%' . addcslashes($text, '%_\\') . '%';

        foreach (['folder_number' => 'b.folder_number', 'lastname' => 'b.lastname', 'fname' => 'b.name', 'fathers_name' => 'b.fathers_name'] as $field => $column) {
            if ($value($field) !== '') {
                $where[] = "$column like ?";
                $bindings[] = $like($value($field));
            }
        }
        if ($value('telephone') !== '') {
            $where[] = 'b.telephone = ?';
            $bindings[] = $value('telephone');
        }
        if ($value('birth_date') !== '') {
            $where[] = 'b.birth_date = ?';
            $bindings[] = $dbDate($value('birth_date'));
        }
        if ($value('origin_country') !== '') {
            $where[] = 'b.origin_country = ?';
            $bindings[] = $value('origin_country');
        }
        $ids = [
            'medical_location_id' => 'mv.medical_location_id',
            'marital_status_id' => 'b.marital_status_id',
            'legal_status_id' => 'bls.legal_lookup_id',
            'education_id' => 'b.education_id',
            'gender_id' => 'b.gender_id',
            'work_title_id' => 'b.work_title_id',
            'incident_type_id' => 'mv.medical_incident_id',
            'examination_results_id' => 'mer.results_lookup_id',
        ];
        foreach ($ids as $field => $column) {
            if ($id($field) !== 0) {
                $where[] = "$column = ?";
                $bindings[] = $id($field);
            }
        }
        if ($value('drug') !== '') {
            $drugId = $this->getMedicationIdFromName($value('drug'));
            if ($drugId !== null) {
                $where[] = 'mm.medication_lookup_id = ?';
                $bindings[] = $drugId;
            }
        }
        if ($value('doctor_name') !== '') {
            $doctorIds = $this->getDoctorIdFromName($value('doctor_name'));
            if ($doctorIds !== []) {
                $where[] = 'mv.doctor_id in (' . implode(',', array_fill(0, legacy_count($doctorIds), '?')) . ')';
                array_push($bindings, ...$doctorIds);
            }
        }
        if ($value('incident_from') !== '' && $value('incident_to') !== '') {
            [$from, $to] = [$value('incident_from'), $value('incident_to')];
            // When the dates are in the wrong order, swap them.
            if ($datesHelper->getDifferenceInDays($from, $to) < 0) {
                [$from, $to] = [$to, $from];
            }
            $where[] = 'mv.medical_visit_date between ? and ?';
            $bindings[] = $dbDate($from);
            $bindings[] = $dbDate($to);
        }

        // Criteria on the computed columns of each beneficiary.
        $outerWhere = [];
        $outerBindings = [];
        if ($value('age') !== '' && ctype_digit($value('age'))) {
            $outerWhere[] = 'age_in_years = ?';
            $outerBindings[] = (int) $value('age');
        }
        if ($value('incidents_number') !== '' && ctype_digit($value('incidents_number'))) {
            $outerWhere[] = 'incidents_counter = ?';
            $outerBindings[] = (int) $value('incidents_number');
        }
        if ($value('insertion_date') !== '') {
            $outerWhere[] = 'created_at_date = ?';
            $outerBindings[] = $dbDate($value('insertion_date'));
        }

        if ($where === [] && $outerWhere === []) {
            Log::info("No parameters passed from benefiter search form!");
            return null;
        }

        $query = "select * from (" . $select . " where " . ($where === [] ? '1=1' : implode(' and ', $where)) . $groupBy . ") as median_table";
        if ($outerWhere !== []) {
            $query .= ' where ' . implode(' and ', $outerWhere);
        }

        return \DB::select($query, array_merge($bindings, $outerBindings));
    }

    // returns all the marital statuses stored in DB
    public function getAllMaritalStatuses(){
        return \DB::table('marital_status_lookup')->get();
    }

    // returns all the legal statuses stored in DB
    public function getAllLegalStatuses(){
        return \DB::table('legal_status_lookup')->get();
    }

    // returns all the education titles stored in DB
    public function getAllEducationTitles(){
        return \DB::table('education_lookup')->get();
    }

    // returns all the work titles stored in DB
    public function getAllWorkTitles(){
        return \App\Models\Folders\WorkTitle::query()->get();
    }

    // returns all the medical incident types stored in DB
    public function getAllMedicalIncidentTypes(){
        return \DB::table('medical_incident_type_lookup')->get();
    }

    // returns all the medical examination results stored in DB
    public function getAllMedicalExaminationResults(){
        return \DB::table('medical_examination_results_lookup')->get();
    }

    // get medication id from name
    private function getMedicationIdFromName($drug){
        $tmp = \DB::table('medical_medication_lookup')->where('description', 'like', '%' . addcslashes($drug, '%_\\') . '%')->first();
        if($tmp != null) {
            Log::info("Returning the drug id. [=" . $tmp->id . "]");
            return $tmp->id;
        } else {
            Log::error("Couldn't find the drug id.");
            return null;
        }
    }

    /**
     * The ids of the doctors (and administrators) whose first or last name contains the text.
     *
     * @return list<int>
     */
    private function getDoctorIdFromName(string $doctorName): array
    {
        $like = '%' . addcslashes($doctorName, '%_\\') . '%';

        return \DB::table('users')
            ->whereIn('user_role_id', [1, 2])
            ->where(fn ($query) => $query->where('lastname', 'like', $like)->orWhere('name', 'like', $like))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }
}
