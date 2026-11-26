/**
 * Psychosocial folder: the forms to add and to change a session open with a button.
 * A session is deleted after the user confirms it in a dialog.
 */
(() => {
    'use strict';

    const { hide, slideToggle } = window.Benefile;
    const { openModal } = window.BenefileUi;

    document.querySelectorAll('.new-session, .edit-session-div').forEach(hide);

    document.getElementById('add-new-session')?.addEventListener('click', () => {
        document.querySelectorAll('.new-session').forEach(slideToggle);
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (button?.classList.contains('edit-session')) {
            slideToggle(button.closest('.div-table-row')?.nextElementSibling);
        } else if (button?.classList.contains('delete-session')) {
            const modal = document.getElementById('delete-session-modal');
            modal?.querySelector('.delete-session-form')?.setAttribute('action', button.dataset.url);
            openModal(modal, button);
        }
    });
})();
