/**
 * Shared code of the main panel: the side menu, requests to the server,
 * tables with sorting, search and paging, and small display helpers.
 *
 * Other scripts use it as window.Benefile.
 * The texts come from the page (#benefile-texts), in the language of the page.
 */
(() => {
    'use strict';

    const textsElement = document.getElementById('benefile-texts');
    const texts = textsElement ? JSON.parse(textsElement.textContent) : {};
    const baseUrl = (document.body.dataset.url || '').replace(/\/$/, '');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    const isHidden = (element) => !element || element.getClientRects().length === 0;

    const show = (element) => {
        if (element) {
            element.style.display = '';
            if (isHidden(element)) {
                element.style.display = 'block';
            }
        }
    };

    const hide = (element) => {
        if (element) {
            element.style.display = 'none';
        }
    };

    const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /** Opens or closes a section with a short vertical animation. */
    function slideToggle(element) {
        if (!element) {
            return;
        }
        const opening = isHidden(element);
        if (opening) {
            show(element);
        }
        if (reducedMotion()) {
            if (!opening) {
                hide(element);
            }
            return;
        }
        const height = `${element.scrollHeight}px`;
        const frames = opening ? [{ height: '0px', overflow: 'hidden' }, { height, overflow: 'hidden' }] : [{ height, overflow: 'hidden' }, { height: '0px', overflow: 'hidden' }];
        element.animate(frames, { duration: 400, easing: 'ease-in-out' }).finished.then(() => {
            if (!opening) {
                hide(element);
            }
        });
    }

    /** Hides an element after some time, with a fade. */
    function fadeOutLater(element, delay) {
        if (!element) {
            return;
        }
        window.setTimeout(() => {
            if (reducedMotion()) {
                hide(element);
                return;
            }
            element.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 400 }).finished.then(() => hide(element));
        }, delay);
    }

    /** Sends a POST request with the CSRF token. The data is an object or a FormData. */
    async function post(url, data = {}) {
        const body = data instanceof FormData ? data : new URLSearchParams(Object.entries(data).map(([key, value]) => [key, value ?? '']));
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json, text/html' },
            credentials: 'same-origin',
            body,
        });
        if (!response.ok) {
            throw new Error(`${response.status} ${response.statusText}`);
        }
        return response;
    }

    /** Sends a GET request and reads the answer as JSON. An empty answer gives null. */
    async function getJson(url, params = {}) {
        const address = new URL(url, window.location.href);
        Object.entries(params).forEach(([key, value]) => address.searchParams.append(key, value ?? ''));
        const response = await fetch(address, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!response.ok) {
            throw new Error(`${response.status} ${response.statusText}`);
        }
        const text = await response.text();
        return text.trim() === '' ? null : JSON.parse(text);
    }

    /** Shows a turning icon over a container. The function that it returns removes it. */
    function showLoading(container) {
        if (!container) {
            return () => {};
        }
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.setAttribute('role', 'status');
        const spinner = document.createElement('span');
        spinner.className = 'loading-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        const label = document.createElement('span');
        label.className = 'sr-only';
        label.textContent = texts.loading ?? '';
        overlay.append(spinner, label);
        container.classList.add('loading-container');
        container.append(overlay);
        return () => {
            overlay.remove();
            container.classList.remove('loading-container');
        };
    }

    /**
     * Gives a table sorting, search and paging (simple-datatables).
     * Without paging, the table shows all its rows. At the start, the rows are in the order of the first column.
     * The rows are drawn again on each change: use event delegation for the elements in the cells.
     */
    function dataTable(tableOrSelector, { paging = true } = {}) {
        const table = typeof tableOrSelector === 'string' ? document.querySelector(tableOrSelector) : tableOrSelector;
        if (!table || !window.simpleDatatables) {
            return null;
        }
        const labels = texts.table ?? {};
        const dataTableInstance = new window.simpleDatatables.DataTable(table, {
            paging,
            perPage: 10,
            perPageSelect: paging ? [10, 25, 50, 100] : false,
            labels: {
                searchLabel: labels.search_label,
                placeholder: labels.placeholder,
                searchTitle: labels.search_title,
                sortHint: labels.sort_hint,
                pageTitle: labels.page_title,
                perPage: labels.per_page,
                noRows: labels.no_rows,
                noResults: labels.no_results,
                info: labels.info,
            },
        });
        if (dataTableInstance.data.data.length > 1) {
            dataTableInstance.columns.sort(0, 'asc');
        }
        return dataTableInstance;
    }

    /** Marks the menu item of the page (data-menu of the body), and opens the submenu that holds it. */
    function markMenuItem() {
        const item = document.body.dataset.menu ? document.getElementById(document.body.dataset.menu) : null;
        if (!item) {
            return;
        }
        document.querySelectorAll('#menu li.purple-background').forEach((other) => other.classList.remove('purple-background'));
        item.classList.remove('hide');
        item.classList.add('purple-background');
        if (item.classList.contains('child')) {
            setSubmenu(true);
        }
    }

    /** Opens or closes the submenu "Registration". */
    function setSubmenu(open) {
        document.querySelectorAll('#menu li.child').forEach((child) => child.classList.toggle('hide', !open));
        const icon = document.querySelector('#register-benefiter div i');
        icon?.classList.toggle('glyphicon-chevron-down', open);
        icon?.classList.toggle('glyphicon-chevron-right', !open);
    }

    document.getElementById('register-benefiter')?.addEventListener('click', () => {
        const icon = document.querySelector('#register-benefiter div i');
        setSubmenu(icon?.classList.contains('glyphicon-chevron-right') ?? false);
    });

    document.querySelectorAll('#menu ul li').forEach((item) => {
        item.addEventListener('click', () => {
            if (!item.classList.contains('purple-background')) {
                document.querySelectorAll('#menu li.purple-background').forEach((other) => other.classList.remove('purple-background'));
                item.classList.add('purple-background');
            }
        });
    });

    markMenuItem();

    window.Benefile = { texts, baseUrl, isHidden, show, hide, slideToggle, fadeOutLater, post, getJson, showLoading, dataTable };
})();
