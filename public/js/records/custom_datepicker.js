/**
 * Date fields (.date-input) open a calendar (vanillajs-datepicker).
 * The format is dd-mm-yyyy, because the server reads this format.
 * Fields that a script adds later get the calendar too, on their first focus.
 * The cross icon (.clear-date) next to a field empties it.
 */
(() => {
    'use strict';

    const pickers = new WeakMap();
    const language = document.documentElement.lang === 'el' ? 'el' : 'en';

    function pickerOf(input) {
        if (!pickers.has(input) && window.Datepicker && !input.disabled) {
            pickers.set(input, new window.Datepicker(input, {
                format: 'dd-mm-yyyy',
                language,
                autohide: true,
                todayHighlight: true,
            }));
        }
        return pickers.get(input);
    }

    document.addEventListener('focusin', (event) => {
        const input = event.target;
        if (input instanceof HTMLInputElement && input.classList.contains('date-input') && !pickers.has(input)) {
            pickerOf(input)?.show();
        }
    });

    document.addEventListener('click', (event) => {
        const clear = event.target.closest('.clear-date');
        if (!clear) {
            return;
        }
        const input = clear.closest('div')?.querySelector('.date-input');
        if (!input || input.disabled) {
            return;
        }
        const picker = pickers.get(input);
        if (picker) {
            picker.setDate({ clear: true });
        } else {
            input.value = '';
            input.dispatchEvent(new CustomEvent('changeDate', { bubbles: true }));
        }
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
})();
