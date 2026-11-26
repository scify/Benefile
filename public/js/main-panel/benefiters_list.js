/**
 * List of beneficiaries: a table that sorts and searches and shows all its rows.
 * The bin icon asks for a confirmation before the deletion of a beneficiary.
 */
(() => {
    'use strict';

    const { dataTable } = window.Benefile;
    const { openModal } = window.BenefileUi;

    dataTable('#usersTable-to-activate', { paging: false });

    // The table draws its rows again on each change, so the listener is on the document.
    document.addEventListener('click', (event) => {
        const bin = event.target.closest('.delete-benefiter');
        if (!bin) {
            return;
        }
        event.preventDefault();
        const modal = document.getElementById('delete-benefiter-modal');
        modal?.querySelector('.delete-benefiter-form')?.setAttribute('action', bin.dataset.url);
        openModal(modal, bin);
    });
})();
