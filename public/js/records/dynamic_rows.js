/**
 * Rows that the user adds and removes in a form: languages and referrals of the basic
 * information; chronic conditions, results, medication, hospitalisations, referrals
 * and files of a medical visit.
 *
 * The plus icon copies the first row of its group, with empty fields.
 * The minus icon removes an added row.
 * After a row is added, the document gets the event "benefile:row-added" (detail: row, group).
 */
(() => {
    'use strict';

    const groups = [
        { name: 'language', parent: '#language-wrapper', first: 'language-div', added: 'added-div', add: '.add-lang', remove: '.remove-lang', renameSelects: true },
        { name: 'basic-referral', parent: '#basic_info_referrals', first: 'basic_info_referral', added: 'ref-added-div', add: '.add-ref', remove: '.remove-ref' },
        { name: 'condition', parent: '#chronic-cond', first: 'chronicConditions', added: 'condition-added-div', add: '.add-condition', remove: '.remove-condition' },
        { name: 'lab-result', parent: '#lab-result', first: 'lab-results', added: 'lab-added-div', add: '.add-lab-result', remove: '.remove-lab-result' },
        { name: 'diagnosis', parent: '#diagnosis-result', first: 'diagnosis-results', added: 'diagnosis-added-div', add: '.add-diagnosis-result', remove: '.remove-diagnosis-result' },
        { name: 'hospitalization', parent: '#hospitalization', first: 'hospitalization-div', added: 'hospitalization-added-div', add: '.add-hospitalization', remove: '.remove-hospitalization' },
        { name: 'medication', parent: '#medication', first: 'medicationList', added: 'med-added-div', add: '.add-med', remove: '.remove-med' },
        { name: 'referral', parent: '#referrals', first: 'referral', added: 'ref-added-div', add: '.add-ref', remove: '.remove-ref' },
        { name: 'file', parent: '#upload_file', first: 'uploadFile', added: 'file-added-div', add: '.add-file', remove: '.remove-file' },
    ];

    const counters = new Map(groups.map((group) => [group.name, document.querySelectorAll(`${group.parent} .${group.added}`).length]));

    /** Empties the fields of a copied row. Lists keep their first or preselected choice. */
    function emptyFields(row) {
        row.querySelectorAll('input').forEach((input) => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else if (input.classList.contains('supply_from_praksis_hidden')) {
                input.value = '0';
            } else if (input.type !== 'hidden') {
                input.value = '';
            }
        });
        row.querySelectorAll('textarea').forEach((textarea) => {
            textarea.value = '';
        });
        // A list with search (Tom Select) starts again from its plain select element.
        row.querySelectorAll('.ts-wrapper').forEach((wrapper) => wrapper.remove());
        row.querySelectorAll('select.tomselected').forEach((select) => {
            select.classList.remove('tomselected', 'ts-hidden-accessible');
            select.removeAttribute('tabindex');
            select.removeAttribute('hidden');
            select.replaceChildren();
        });
    }

    function addRow(group, container) {
        const first = container.querySelector(`.${group.first}`);
        if (!first) {
            return;
        }
        const row = first.cloneNode(true);
        row.classList.replace(group.first, group.added);
        emptyFields(row);
        row.querySelectorAll('.color-green').forEach((icon) => {
            icon.style.display = 'none';
        });
        row.querySelectorAll('.color-red').forEach((icon) => {
            icon.classList.remove('hide-element');
            icon.style.display = 'inline';
        });
        row.querySelectorAll('.saved-file').forEach((element) => {
            element.style.display = 'none';
        });
        row.querySelectorAll('.new-upload-file').forEach((element) => {
            element.style.display = '';
        });
        if (group.renameSelects) {
            const count = counters.get(group.name) + 1;
            counters.set(group.name, count);
            row.querySelectorAll('.language-selection, .level-selection').forEach((select) => {
                select.name += count;
            });
        }
        container.append(row);
        document.dispatchEvent(new CustomEvent('benefile:row-added', { detail: { row, group: group.name } }));
    }

    document.addEventListener('click', (event) => {
        for (const group of groups) {
            const container = event.target.closest(group.parent);
            if (!container) {
                continue;
            }
            if (event.target.closest(group.add)) {
                event.preventDefault();
                addRow(group, container);
                return;
            }
            if (event.target.closest(group.remove)) {
                event.preventDefault();
                event.target.closest(`.${group.added}`)?.remove();
                return;
            }
        }
    });

    // Rows that the server drew as added rows show the minus icon, not the plus icon.
    groups.forEach((group) => {
        document.querySelectorAll(`${group.parent} .${group.added}`).forEach((row) => {
            row.querySelectorAll('.color-green').forEach((icon) => {
                icon.style.display = 'none';
            });
            if (group.name === 'language') {
                row.querySelectorAll('.color-red').forEach((icon) => {
                    icon.classList.remove('hide-element');
                    icon.style.display = 'inline';
                });
            }
        });
    });
})();
