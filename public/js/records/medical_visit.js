/**
 * Medical folder: the form of a new or changed medical visit, the lists with search
 * for medicines and ICD-10 conditions (Tom Select), the window of a past visit,
 * and the medical history table.
 */
(() => {
    'use strict';

    const { baseUrl, texts, getJson, show, hide, slideToggle, fadeOutLater, dataTable } = window.Benefile;
    const MIN_SEARCH_LENGTH = 3;
    const MAX_UPLOAD_BYTES = 52428800;

    /**
     * Turns a select into a list with search. The options come from the server.
     * toOption gives { value, text } for each item of the answer.
     */
    function remoteSelect(select, { url, placeholder, multiple, toOption }) {
        const escape = (value) => {
            const element = document.createElement('span');
            element.textContent = value;
            return element.innerHTML;
        };
        return new window.TomSelect(select, {
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            maxItems: multiple ? null : 1,
            placeholder,
            allowEmptyOption: false,
            plugins: {
                clear_button: { title: texts.clear ?? '' },
                ...(multiple ? { remove_button: { title: texts.remove ?? '' } } : {}),
            },
            shouldLoad: (query) => query.length >= MIN_SEARCH_LENGTH,
            load: (query, callback) => {
                getJson(url, { q: query })
                    .then((items) => callback((items ?? []).map(toOption)))
                    .catch(() => callback());
            },
            render: {
                no_results: () => `<div class="no-results">${escape(texts.no_results ?? '')}</div>`,
                loading: () => `<div class="spinner" role="status" aria-label="${escape(texts.loading ?? '')}"></div>`,
            },
        });
    }

    // Medicines: the free text field shows only when no medicine of the list is chosen.
    function medicationSelect(select) {
        const row = select.closest('.medicationList, .med-added-div') ?? select.parentElement;
        const freeText = () => row.querySelectorAll('.medication_other_name');
        const update = (value) => freeText().forEach(value ? hide : show);
        const list = remoteSelect(select, {
            url: `${baseUrl}/benefiter/getMedicationList`,
            placeholder: document.body.dataset.medicationPlaceholder ?? '',
            multiple: false,
            toOption: (item) => ({ value: String(item.id), text: String(item.description ?? '') }),
        });
        list.on('change', update);
        update(list.getValue());
        return list;
    }

    // Clinical results: the description shows while the list is open, and stays when a condition is chosen.
    function conditionSelect(select) {
        const description = select.parentElement.querySelector('textarea');
        const list = remoteSelect(select, {
            url: `${baseUrl}/benefiter/getIC10List`,
            placeholder: document.getElementById('clinical-results-div')?.dataset.placeholderName ?? '',
            multiple: true,
            toOption: (item) => ({ value: String(item.id), text: `${item.code ?? ''}: ${item.description ?? ''}` }),
        });
        list.on('focus', () => show(description));
        list.on('blur', () => {
            if (list.items.length === 0) {
                hide(description);
            }
        });
        return list;
    }

    document.querySelectorAll('select[id^="clinical-select-"]').forEach(conditionSelect);
    document.querySelectorAll('select[id^="medicinal_name_"]').forEach(medicationSelect);

    let medicationCount = document.querySelectorAll('#medication select').length;
    document.addEventListener('benefile:row-added', (event) => {
        if (event.detail.group !== 'medication') {
            return;
        }
        const select = event.detail.row.querySelector('select[name="medication_name_from_lookup[]"]');
        if (select) {
            medicationCount += 1;
            select.id = `medicinal_name_${medicationCount}`;
            medicationSelect(select);
        }
    });

    // Supply from the organisation: the hidden field sends 1 or 0.
    document.addEventListener('change', (event) => {
        const checkbox = event.target;
        if (checkbox instanceof HTMLInputElement && checkbox.classList.contains('supply_from_praksis')) {
            const hidden = checkbox.parentElement.querySelector('.supply_from_praksis_hidden');
            if (hidden) {
                hidden.value = checkbox.checked ? '1' : '0';
            }
        }
    });

    // Saved files: the remove icon offers a new upload in their place.
    document.querySelectorAll('.new-upload-file').forEach(hide);
    document.addEventListener('click', (event) => {
        const remove = event.target.closest('.remove-uploaded-file');
        if (remove) {
            show(remove.closest('.padding-left-right-15')?.querySelector('.new-upload-file'));
            hide(remove.closest('.saved-file'));
        }
    });

    // The form of a new visit stays open when the server found errors in it.
    const newVisit = document.getElementById('new-medical-visit');
    if (newVisit && !document.querySelector('.alert.alert-danger')) {
        hide(newVisit);
    }
    document.getElementById('new-med-visit-button')?.addEventListener('click', () => {
        slideToggle(newVisit);
        newVisit?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.querySelectorAll('div.success-message, div.unsuccess-message, i.glyphicon-ok.updated-visit').forEach((element) => fadeOutLater(element, 5000));

    // A past visit: the window gets its content from the server (HTML that the server escapes).
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button.medical_visit_from_history[data-url]');
        if (!button) {
            return;
        }
        const content = document.getElementById('medical-visit-modal-content');
        const address = new URL(button.dataset.url, window.location.href);
        address.searchParams.set('current_medical_visit', button.value);
        fetch(address, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((response) => (response.ok ? response.text() : Promise.reject(new Error(String(response.status)))))
            .then((html) => {
                if (content) {
                    content.innerHTML = html;
                }
            })
            .catch(() => {});
    });

    // The files of one visit may have 50 MB in total.
    document.querySelectorAll('form').forEach((form) => {
        if (!form.querySelector('input[type=file]')) {
            return;
        }
        form.addEventListener('submit', (event) => {
            const total = [...form.querySelectorAll('input[type=file]')].reduce((sum, input) => sum + (input.files?.[0]?.size ?? 0), 0);
            if (total > MAX_UPLOAD_BYTES) {
                event.preventDefault();
                window.alert(document.getElementById('upload_file')?.dataset.formSubmitError ?? '');
            }
        });
    });

    dataTable('#benefiter_referrals_history');
})();
