/**
 * Search of beneficiaries: the form asks the server in the background,
 * and the page shows the results in a table that downloads as a CSV file.
 *
 * The values come from the database, so the rows are built with textContent, never as HTML.
 */
(() => {
    'use strict';

    const { getJson, show, hide, showLoading } = window.Benefile;
    const form = document.getElementById('search-form');
    const results = document.getElementById('search-results');
    const table = document.getElementById('results');
    const texts = document.querySelector('.benefiters-search')?.dataset ?? {};
    if (!form || !results || !table) {
        return;
    }

    const field = (name) => form.querySelector(`[name="${name}"]`);
    const valueOf = (name) => field(name)?.value ?? '';
    const trimmed = (name) => valueOf(name).trim();

    function searchValues() {
        return {
            folder_number: valueOf('folder_number'),
            lastname: valueOf('lastname'),
            fname: valueOf('name'),
            fathers_name: valueOf('fathers_name'),
            gender_id: form.querySelector('input[name="gender_id"]:checked')?.value ?? '',
            telephone: valueOf('telephone'),
            birth_date: valueOf('birth_date'),
            origin_country: valueOf('origin_country'),
            medical_location_id: valueOf('medical_location_id'),
            marital_status_id: valueOf('marital_status_id'),
            age: trimmed('age'),
            legal_status_id: valueOf('legal_status_id'),
            education_id: valueOf('education_id'),
            work_title_id: valueOf('work_title_id'),
            drug: trimmed('drug'),
            incident_type_id: valueOf('incident_type_id'),
            doctor_name: trimmed('doctor_name'),
            incidents_number: trimmed('incidents_number'),
            examination_results_id: valueOf('examination_results_id'),
            insertion_date: valueOf('insertion_date'),
            incident_from: valueOf('incident_from'),
            incident_to: valueOf('incident_to'),
        };
    }

    const text = (value) => (value === null || value === undefined ? '' : String(value));

    /** Changes a date from yyyy-mm-dd to dd-mm-yyyy. */
    function formatDate(value) {
        if (!value || value === '0000-00-00') {
            return '';
        }
        const [year, month, day] = String(value).split('-');
        return `${day}-${month}-${year}`;
    }

    const yesOrNo = (value) => (String(value) === '0' ? texts.noLang : texts.yesLang);

    /** One row of results. The first four columns are visible, the others go only to the CSV file. */
    function resultRow(benefiter) {
        const visible = [benefiter.folder_number, benefiter.name, benefiter.lastname, benefiter.telephone];
        const hidden = [
            benefiter.fathers_name, benefiter.mothers_name, formatDate(benefiter.birth_date), formatDate(benefiter.arrival_date),
            benefiter.address, benefiter.number_of_children, benefiter.relatives_residence,
            yesOrNo(benefiter.language_interpreter_needed), yesOrNo(benefiter.is_benefiter_working),
            benefiter.country_abandon_reason, benefiter.travel_route, benefiter.travel_duration, formatDate(benefiter.detention_date),
            benefiter.origin_country, benefiter.nationality_country, benefiter.ethnic_group, benefiter.social_history,
            benefiter.marital_status_title, benefiter.education_title, benefiter.legal_working_status, benefiter.work_title,
        ];
        const row = document.createElement('tr');
        const cell = (value, className) => {
            const element = document.createElement('td');
            element.className = className;
            element.textContent = text(value);
            row.append(element);
        };
        visible.forEach((value) => cell(value, 'printable'));
        hidden.forEach((value) => cell(value, 'hide printable'));

        const link = document.createElement('a');
        const address = new URL(results.dataset.url, window.location.href);
        address.pathname = address.pathname.replace('/-1/', `/${encodeURIComponent(text(benefiter.id))}/`);
        link.href = address.href;
        link.className = 'simple-button display-block text-center';
        link.target = '_blank';
        link.rel = 'noopener';
        link.textContent = results.dataset.viewFolders ?? '';
        const linkCell = document.createElement('td');
        linkCell.append(link);
        row.append(linkCell);
        return row;
    }

    function displayResults(benefiters) {
        if (!Array.isArray(benefiters) || benefiters.length === 0) {
            show(results.querySelector('.state-no-results'));
            return;
        }
        table.tBodies[0].append(...benefiters.map(resultRow));
        show(results.querySelector('.state-results'));
    }

    async function search(values) {
        document.querySelector('.benefiters-search')?.classList.remove('margin-bottom-300px');
        show(results);
        results.querySelectorAll('.state').forEach(hide);
        const loading = results.querySelector('.state-loading');
        show(loading);
        const stopLoading = showLoading(loading);
        try {
            const benefiters = await getJson(form.action, values);
            table.tBodies[0].replaceChildren();
            displayResults(benefiters);
        } catch {
            show(results.querySelector('.state-error'));
        } finally {
            stopLoading();
            hide(loading);
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        search(searchValues());
    });

    document.getElementById('fetch-all-btn')?.addEventListener('click', (event) => {
        event.preventDefault();
        search({ all_required: true });
    });

    // A folder number from the search of the side menu starts the search at once.
    if (valueOf('folder_number').length > 0) {
        search(searchValues());
    }

    /** Date and time for the name of the CSV file, for example 2024-03-07_9_5_12. */
    function fileDate() {
        const now = new Date();
        const pad = (number) => String(number).padStart(2, '0');
        return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}_${now.getHours()}_${now.getMinutes()}_${now.getSeconds()}`;
    }

    const quoted = (value) => `"${value.trim().replace(/\s+/g, ' ').replace(/"/g, '""')}"`;

    // The CSV file: one column for each printable column, separated by tabs.
    document.getElementById('download-link')?.addEventListener('click', (event) => {
        const header = [...table.querySelectorAll('thead > tr > th.printable')].map((th) => quoted(th.textContent)).join('\t');
        const rows = [...table.querySelectorAll('tbody > tr')]
            .map((tr) => [...tr.querySelectorAll('td.printable')].map((td) => quoted(td.textContent)).join('\t'))
            .filter((line) => line !== '');
        const csv = [header, ...rows].join('\r\n');
        const link = event.currentTarget;
        link.download = `${fileDate()}_Benefile_export.csv`;
        link.href = `data:application/csv;charset=utf-8,${encodeURIComponent(csv)}`;
        link.target = '_blank';
    });
})();
