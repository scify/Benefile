/**
 * Basic information of a beneficiary: incidents (saved in the background),
 * work, months since detention, deletion of referrals, and the history tables.
 */
(() => {
    'use strict';

    const { baseUrl, post, show, hide, slideToggle, dataTable } = window.Benefile;
    const { openModal } = window.BenefileUi;

    /** Shows the months since the detention date (dd-mm-yyyy). */
    function displayMonthsPassed(value) {
        const output = document.getElementById('months-passed');
        if (!output) {
            return;
        }
        const match = /^(\d{2})-(\d{2})-(\d{4})/.exec(value ?? '');
        if (!match) {
            output.textContent = '';
            return;
        }
        const detention = new Date(Number(match[3]), Number(match[2]) - 1, Number(match[1]));
        const difference = Date.now() - detention.getTime();
        if (difference < 0) {
            output.textContent = '';
            return;
        }
        const months = Math.floor(difference / 1000 / 60 / 60 / 24 / 30);
        const unit = months === 1 ? output.dataset.month : output.dataset.months;
        output.textContent = `(${months} ${unit})`;
    }

    const reload = () => window.location.reload();

    // Incidents
    document.querySelectorAll('.new-occurrence, .edit-occurrence-div').forEach(hide);
    document.getElementById('add-new-occurrence')?.addEventListener('click', () => {
        document.querySelectorAll('.new-occurrence').forEach(slideToggle);
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) {
            return;
        }
        if (button.classList.contains('edit-occurrence')) {
            slideToggle(button.closest('.div-table-row')?.nextElementSibling);
        } else if (button.classList.contains('delete-occurrence')) {
            const modal = document.querySelector('.delete-occurrence-modal');
            const confirm = modal?.querySelector('.delete-occurrence-confirm-button');
            if (confirm) {
                confirm.dataset.benefiterId = button.dataset.benefiterId;
                confirm.dataset.occurrenceId = button.dataset.occurrenceId;
            }
            openModal(modal, button);
        } else if (button.classList.contains('delete-occurrence-confirm-button')) {
            post(`${baseUrl}/benefiter/${button.dataset.benefiterId}/delete-occurrence/${button.dataset.occurrenceId}`).then(reload);
        } else if (button.classList.contains('new-occurrence-submit')) {
            post(`${baseUrl}/benefiter/${button.dataset.benefiterId}/new-occurrence-save`, {
                occurrence_date: document.getElementById('occurrence_date')?.value,
                occurrences_comments: document.getElementById('occurrences_comments')?.value,
                benefiter_id: document.getElementById('benefiter_id')?.value,
            }).then(reload);
        } else if (button.classList.contains('edit-occurrence-submit')) {
            const id = button.dataset.occurrenceId;
            post(`${baseUrl}/benefiter/${button.dataset.benefiterId}/edit-occurrence/${id}`, {
                edited_occurrence_date: document.getElementById(`edited_occurrence_date_${id}`)?.value,
                edited_occurrences_comments: document.getElementById(`edited_occurrences_comments_${id}`)?.value,
            }).then(reload);
        } else if (button.classList.contains('delete-referral')) {
            const modal = document.getElementById('delete-session-modal');
            modal?.querySelector('.delete-session-form')?.setAttribute('action', button.dataset.url);
            openModal(modal, button);
        }
    });

    // Work: the details show only when the beneficiary works.
    const workDetails = () => [document.getElementById('working_legally_div'), document.getElementById('working_title_div')];
    workDetails().forEach(document.getElementById('show_work_legally')?.checked ? show : hide);
    document.getElementById('show_work_legally')?.addEventListener('change', () => workDetails().forEach(show));
    document.getElementById('hide_work_legally')?.addEventListener('change', () => {
        workDetails().forEach(hide);
        const title = document.getElementById('working_title');
        if (title) {
            title.value = '';
        }
    });

    // Months since detention
    const detention = document.getElementById('detention-date');
    if (detention) {
        displayMonthsPassed(detention.value);
        ['change', 'changeDate', 'focusout'].forEach((type) => detention.addEventListener(type, () => displayMonthsPassed(detention.value)));
    }

    dataTable('#benefiter_referrals_history');
    dataTable('#folders-update-history');
})();
