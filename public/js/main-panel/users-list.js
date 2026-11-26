/**
 * Users list: three tabs (to activate, active, deactivated), each with a table
 * that sorts and searches and shows all its rows.
 */
(() => {
    'use strict';

    const { show, hide, dataTable } = window.Benefile;

    // [tab, results]
    const tabs = [
        ['to-activate', 'results-to-activate'],
        ['active', 'results-active'],
        ['inactive', 'results-deactiveted'],
    ];

    function selectTab(tabId) {
        tabs.forEach(([id, results]) => {
            const selected = id === tabId;
            const tab = document.getElementById(id);
            tab?.classList.toggle('active', selected);
            ['pink-border-bottom', 'white', 'bold'].forEach((name) => tab?.classList.toggle(name, selected));
            tab?.setAttribute('aria-selected', String(selected));
            (selected ? show : hide)(document.getElementById(results));
        });
    }

    tabs.forEach(([id]) => {
        const tab = document.getElementById(id);
        if (!tab) {
            return;
        }
        tab.setAttribute('role', 'tab');
        tab.tabIndex = 0;
        tab.addEventListener('click', () => selectTab(id));
        tab.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                selectTab(id);
            }
        });
    });

    ['#usersTable-to-activate', '#usersTable-active', '#usersTable-deactivated'].forEach((table) => dataTable(table, { paging: false }));
    selectTab('to-activate');
})();
