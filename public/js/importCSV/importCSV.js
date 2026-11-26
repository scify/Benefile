/**
 * CSV import: Dropzone sends the file. The server answers with a list of errors,
 * empty when the import worked.
 *
 * Dropzone reads Dropzone.options.dropzone when the page has loaded, so this script sets it at once.
 */
(() => {
    'use strict';

    const { show, hide, fadeOutLater, showLoading } = window.Benefile;
    const success = document.querySelector('div.success-message');
    const failure = document.querySelector('div.unsuccess-message');
    const errors = document.getElementById('error-list');
    let stopLoading = () => {};

    hide(success);
    hide(failure);

    const showFor = (element, milliseconds) => {
        show(element);
        fadeOutLater(element, milliseconds);
    };

    window.Dropzone.options.dropzone = {
        acceptedFiles: '.csv',
        maxFiles: 1,
        maxFilesize: 50,
        sending() {
            stopLoading = showLoading(document.getElementById('main-window'));
        },
        success(file, messages) {
            const list = Array.isArray(messages) ? messages : [];
            if (list.length > 0 && errors) {
                errors.replaceChildren(...list.map((message) => {
                    const item = document.createElement('li');
                    item.textContent = String(message);
                    return item;
                }));
                errors.parentElement?.classList.add('alert', 'alert-danger');
            } else {
                showFor(success, 7000);
            }
        },
        canceled() {
            showFor(failure, 7000);
        },
        complete() {
            stopLoading();
        },
    };

    document.getElementById('import-another')?.addEventListener('click', () => window.location.reload());
})();
