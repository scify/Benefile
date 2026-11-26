/**
 * New beneficiary: after a change of the name, last name, father's name or birth date,
 * the server looks for folders with the same values, and the page lists them.
 */
(() => {
    'use strict';

    const { post, show, hide, isHidden } = window.Benefile;
    const box = document.getElementById('duplicate-folders');
    const section = document.querySelector('.personal-info[data-url]');
    if (!box || !section) {
        return;
    }

    /** The address of the basic information of another folder. */
    function folderAddress(id) {
        const address = new URL(window.location.href);
        address.pathname = address.pathname.replace('/-1/', `/${encodeURIComponent(id)}/`);
        address.search = '';
        return address.href;
    }

    function displayPossibleDuplicates(folders) {
        if (!Array.isArray(folders) || folders.length === 0) {
            hide(box);
            return;
        }
        const header = document.createElement('div');
        header.className = 'alert-header';
        const title = document.createElement('strong');
        title.textContent = box.dataset.title ?? '';
        header.append(title);
        box.replaceChildren(header);
        folders.forEach((folder) => {
            const link = document.createElement('a');
            link.style.color = 'black';
            link.href = folderAddress(folder.id);
            link.textContent = box.dataset.displayLink ?? '';
            const description = [`${folder.name ?? ''} ${folder.lastname ?? ''}`, folder.fathers_name ?? '', folder.birth_date ?? ''].join(', ');
            box.append(`${description}, `, link, document.createElement('br'));
        });
        if (isHidden(box)) {
            show(box);
        }
    }

    async function checkIfBenefiterAlreadyExists() {
        const data = {};
        document.querySelectorAll('.uniqueness').forEach((field) => {
            if (field.value.length > 0) {
                data[field.name] = field.value;
            }
        });
        try {
            const response = await post(section.dataset.url, data);
            const text = await response.text();
            displayPossibleDuplicates(text.trim() === '' ? [] : JSON.parse(text));
        } catch {
            // The check only helps the user. Without an answer, the form stays as it is.
        }
    }

    document.querySelectorAll('.uniqueness').forEach((field) => {
        field.addEventListener('change', checkIfBenefiterAlreadyExists);
        field.addEventListener('changeDate', checkIfBenefiterAlreadyExists);
    });
})();
