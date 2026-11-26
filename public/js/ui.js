/**
 * The JavaScript part of Bootstrap 3 that the pages use, in plain JavaScript.
 *
 * It reads the Bootstrap 3 data attributes, so the markup and the Bootstrap 3 CSS stay:
 * - data-toggle="modal" with data-target (or href) opens a dialog. data-dismiss="modal" closes it.
 *   A click on the dark area and the Escape key also close it. The focus goes back to the opener.
 * - data-toggle="tab" shows a tab pane.
 * - data-toggle="collapse" opens and closes a section.
 * - data-toggle="dropdown" opens and closes a menu.
 *
 * Other scripts use window.BenefileUi.openModal(modal, opener) and window.BenefileUi.closeModal(modal).
 */
(() => {
    'use strict';

    const TRANSITION_MS = 300;
    const openers = new WeakMap();

    const targetOf = (trigger) => {
        const selector = trigger.getAttribute('data-target') || trigger.getAttribute('href');
        if (!selector || selector === '#' || !selector.startsWith('#')) {
            return null;
        }
        return document.querySelector(selector);
    };

    const openModals = () => [...document.querySelectorAll('.modal.in')];

    function openModal(modal, opener = document.activeElement) {
        if (!modal || modal.classList.contains('in')) {
            return;
        }
        openers.set(modal, opener);
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade';
        document.body.append(backdrop);
        modal.backdropElement = backdrop;

        const scrollbar = window.innerWidth - document.documentElement.clientWidth;
        if (scrollbar > 0) {
            document.body.style.paddingRight = `${scrollbar}px`;
        }
        document.body.classList.add('modal-open');
        modal.style.display = 'block';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.removeAttribute('aria-hidden');
        modal.setAttribute('tabindex', '-1');
        modal.scrollTop = 0;
        // Read the layout once, so that the fade transition starts from the hidden state.
        void modal.offsetWidth;
        backdrop.classList.add('in');
        modal.classList.add('in');
        modal.focus();
    }

    function closeModal(modal) {
        if (!modal || !modal.classList.contains('in')) {
            return;
        }
        modal.classList.remove('in');
        modal.backdropElement?.classList.remove('in');
        window.setTimeout(() => {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
            modal.backdropElement?.remove();
            modal.backdropElement = null;
            if (openModals().length === 0) {
                document.body.classList.remove('modal-open');
                document.body.style.paddingRight = '';
            }
            const opener = openers.get(modal);
            if (opener instanceof HTMLElement && document.contains(opener)) {
                opener.focus();
            }
        }, TRANSITION_MS);
    }

    function showTab(trigger) {
        const pane = targetOf(trigger);
        const item = trigger.closest('li');
        item?.parentElement?.querySelectorAll(':scope > li.active').forEach((other) => other.classList.remove('active'));
        item?.classList.add('active');
        trigger.setAttribute('aria-selected', 'true');
        if (pane) {
            pane.parentElement?.querySelectorAll(':scope > .tab-pane.active').forEach((other) => other.classList.remove('active', 'in'));
            pane.classList.add('active', 'in');
        }
    }

    function toggleCollapse(trigger) {
        const section = targetOf(trigger);
        if (!section) {
            return;
        }
        const open = section.classList.toggle('in');
        trigger.classList.toggle('collapsed', !open);
        trigger.setAttribute('aria-expanded', String(open));
    }

    function closeDropdowns(except = null) {
        document.querySelectorAll('.dropdown.open, .btn-group.open').forEach((menu) => {
            if (menu !== except) {
                menu.classList.remove('open');
                menu.querySelector('[data-toggle="dropdown"]')?.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-toggle], [data-dismiss="modal"]');
        if (!trigger) {
            closeDropdowns();
            const modal = event.target.closest('.modal.in');
            if (modal && event.target === modal && modal.getAttribute('data-backdrop') !== 'static') {
                closeModal(modal);
            }
            return;
        }
        if (trigger.getAttribute('data-dismiss') === 'modal') {
            event.preventDefault();
            closeModal(trigger.closest('.modal'));
            return;
        }
        switch (trigger.getAttribute('data-toggle')) {
            case 'modal':
                event.preventDefault();
                openModal(targetOf(trigger), trigger);
                break;
            case 'tab':
            case 'pill':
                event.preventDefault();
                showTab(trigger);
                break;
            case 'collapse':
                event.preventDefault();
                toggleCollapse(trigger);
                break;
            case 'dropdown': {
                event.preventDefault();
                const menu = trigger.parentElement;
                closeDropdowns(menu);
                const open = menu.classList.toggle('open');
                trigger.setAttribute('aria-expanded', String(open));
                break;
            }
            default:
                break;
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }
        closeDropdowns();
        const modals = openModals();
        const top = modals[modals.length - 1];
        if (top && top.getAttribute('data-keyboard') !== 'false') {
            closeModal(top);
        }
    });

    // Keeps the focus inside an open dialog, as Bootstrap 3 does.
    document.addEventListener('focusin', (event) => {
        const modals = openModals();
        const top = modals[modals.length - 1];
        if (top && !top.contains(event.target)) {
            top.focus();
        }
    });

    window.BenefileUi = { openModal, closeModal };
})();
