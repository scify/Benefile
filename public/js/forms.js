/**
 * The label of a text field or a text area changes colour while the field has the focus.
 */
(() => {
    'use strict';

    const labelOf = (field) => field.closest('.form-group')?.querySelector('label');

    document.addEventListener('focusin', (event) => {
        const field = event.target;
        if ((field instanceof HTMLInputElement && field.type === 'text') || field instanceof HTMLTextAreaElement) {
            labelOf(field)?.classList.add('focused');
        }
    });

    document.addEventListener('focusout', (event) => {
        const field = event.target;
        if (field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement) {
            labelOf(field)?.classList.remove('focused');
        }
    });
})();
