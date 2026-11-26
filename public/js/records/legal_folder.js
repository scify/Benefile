/**
 * Legal folder: each answer shows or hides the sections that depend on it.
 * The button "new session" opens the form of a lawyer session.
 */
(() => {
    'use strict';

    const { hide, slideToggle, dataTable } = window.Benefile;

    const sections = (selector) => document.querySelectorAll(selector);
    const setVisible = (selector, visible) => sections(selector).forEach((section) => section.classList.toggle('hide', !visible));
    const isChecked = (id) => document.getElementById(id)?.checked ?? false;

    // [radio button, sections it shows, sections it hides]
    const rules = [
        ['asylum', ['.asylum-request'], ['.legal-status-div']],
        ['action-none', [], ['.results']],
        ['action-refusal', ['.results'], []],
        ['procedure-old', [], ['.request-status']],
        ['procedure-new', ['.request-status'], []],
        ['penalty-no', [], ['.penalty-text']],
        ['penalty-yes', ['.penalty-text'], []],
    ];

    // At the start: the state of the saved answers.
    if (isChecked('asylum')) {
        setVisible('.asylum-request', true);
    } else {
        setVisible('.legal-status-div', true);
    }
    ['action-refusal', 'procedure-new', 'penalty-yes'].forEach((id) => {
        const rule = rules.find(([radio]) => radio === id);
        if (isChecked(id)) {
            rule[1].forEach((selector) => setVisible(selector, true));
        }
    });

    document.addEventListener('change', (event) => {
        const radio = event.target;
        if (!(radio instanceof HTMLInputElement)) {
            return;
        }
        if (radio.classList.contains('legal-status')) {
            setVisible('.asylum-request', false);
            setVisible('.legal-status-div', true);
            return;
        }
        const rule = rules.find(([id]) => id === radio.id);
        if (rule) {
            rule[1].forEach((selector) => setVisible(selector, true));
            rule[2].forEach((selector) => setVisible(selector, false));
        }
    });

    sections('.new-session').forEach(hide);
    document.getElementById('add-new-session')?.addEventListener('click', () => sections('.new-session').forEach(slideToggle));

    dataTable('#legal-sessions-history');
})();
