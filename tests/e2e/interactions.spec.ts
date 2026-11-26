import { expect, test, type Page } from '@playwright/test';
import { mkdirSync, truncateSync, writeFileSync } from 'node:fs';
import { dirname } from 'node:path';
import { authFile } from './global-setup';
import { chooseRemoteOption, columnTexts, pickDay, runPhp, tableNextPage, tableSearch, watchErrors } from './helpers';

/**
 * Every behaviour that the JavaScript of the pages gives: menus, tabs, dialogs,
 * lists, dynamic rows, date pickers, lists with search, and the requests in the background.
 * Some tests change the data of the local database.
 */

test.use({ storageState: authFile('admin') });

const unique = () => `${Date.now()}${Math.floor(Math.random() * 1000)}`;

/** Creates a beneficiary through the form, and returns the address of its basic information. */
async function createBenefiter(page: Page, name: string): Promise<string> {
    await page.goto('/benefiter/-1/basic-info');
    await page.locator('input[name=folder_number]').fill(`E2E-${unique()}`);
    await page.locator('input[name=lastname]').fill(`${name}-lastname`);
    await page.locator('input[name=name]').fill(name);
    await page.locator('input[name=updated_by_date]').fill('01-02-2024');
    await page.locator('form input[type=submit].submit-button').last().click();
    await expect(page).toHaveURL(/\/benefiter\/\d+\/basic-info/);
    return new URL(page.url()).pathname;
}

test.describe('main menu', () => {
    test('"Registration" opens and closes its submenu', async ({ page }) => {
        await page.goto('/benefiters-list');
        const register = page.locator('#register-benefiter');
        await expect(page.locator('#new-benefiter')).toBeHidden();
        await register.click();
        await expect(page.locator('#new-benefiter')).toBeVisible();
        await expect(page.locator('#import-file')).toBeVisible();
        await expect(register.locator('i')).toHaveClass(/glyphicon-chevron-down/);
        await register.click();
        await expect(page.locator('#new-benefiter')).toBeHidden();
        await expect(register.locator('i')).toHaveClass(/glyphicon-chevron-right/);
    });

    test('each page marks its menu item', async ({ page }) => {
        for (const [path, item] of [['/benefiters-list', '#benefiters-list'], ['/search', '#search'], ['/reports', '#reports'],
            ['/new-location', '#new-location'], ['/main-panel/users-list', '#users-list'], ['/benefiter/1/basic-info', '#edit-benefiter'],
            ['/benefiter/-1/basic-info', '#new-benefiter'], ['/new-benefiter/uploadCSV', '#import-file']]) {
            await page.goto(path);
            await expect(page.locator(item), `${path} marks ${item}`).toHaveClass(/purple-background/);
            await expect(page.locator(item)).toBeVisible();
        }
    });

    test('a label changes colour while its field has the focus', async ({ page }) => {
        await page.goto('/new-location');
        const input = page.locator('input[name=new_location]');
        const label = page.locator('label[for=new_location]');
        await input.focus();
        await expect(label).toHaveClass(/focused/);
        await input.blur();
        await expect(label).not.toHaveClass(/focused/);
    });
});

test.describe('registration page', () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test('the speciality shows only for doctors', async ({ page }) => {
        await page.goto('/auth/register');
        await expect(page.locator('#user-subrole')).toBeHidden();
        await page.locator('select[name=user_role_id]').selectOption('2');
        await expect(page.locator('#user-subrole')).toBeVisible();
        await page.locator('select[name=user_role_id]').selectOption('3');
        await expect(page.locator('#user-subrole')).toBeHidden();
    });
});

test.describe('lists', () => {
    test('the beneficiaries list sorts, filters and shows all rows', async ({ page }) => {
        await page.goto('/benefiters-list');
        const rows = page.locator('#usersTable-to-activate tbody tr');
        const total = await runPhp('echo \\DB::table("benefiters")->whereNull("deleted_at")->count();');
        await expect(rows).toHaveCount(Number(total.trim()));
        await page.locator('#usersTable-to-activate thead th').nth(2).click();
        const ascending = await columnTexts(page, 'usersTable-to-activate', 3);
        expect(ascending).toEqual([...ascending].sort((a, b) => a.localeCompare(b)));
        await page.locator('#usersTable-to-activate thead th').nth(2).click();
        const descending = await columnTexts(page, 'usersTable-to-activate', 3);
        expect(descending).toEqual([...ascending].reverse());
        await tableSearch(page, 'usersTable-to-activate').fill('Ben-lastname-2');
        await expect(rows).toHaveCount(1);
        await expect(rows.first()).toContainText('Ben-name-2');
    });

    test('the users list has three tabs and a search', async ({ page }) => {
        await page.goto('/main-panel/users-list');
        await expect(page.locator('#results-to-activate')).toBeVisible();
        await expect(page.locator('#results-active')).toBeHidden();
        await page.locator('#active').click();
        await expect(page.locator('#active')).toHaveClass(/pink-border-bottom/);
        await expect(page.locator('#to-activate')).not.toHaveClass(/pink-border-bottom/);
        await expect(page.locator('#results-active')).toBeVisible();
        await expect(page.locator('#results-to-activate')).toBeHidden();
        await tableSearch(page, 'usersTable-active').fill('e2e-lawyer');
        await expect(page.locator('#usersTable-active tbody tr')).toHaveCount(1);
        await page.locator('#inactive').click();
        await expect(page.locator('#results-deactiveted')).toBeVisible();
        await expect(page.locator('#results-active')).toBeHidden();
        await page.locator('#to-activate').click();
        await expect(page.locator('#results-to-activate')).toBeVisible();
        await expect(page.locator('#results-deactiveted')).toBeHidden();
    });

    test('the medical history sorts and filters', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        const rows = page.locator('#benefiter_referrals_history tbody tr');
        const count = await rows.count();
        expect(count).toBeGreaterThan(1);
        const header = page.locator('#benefiter_referrals_history thead th').first();
        await header.click();
        const once = (await columnTexts(page, 'benefiter_referrals_history', 1)).map(Number);
        await header.click();
        const twice = (await columnTexts(page, 'benefiter_referrals_history', 1)).map(Number);
        const ascending = [...once].sort((a, b) => a - b);
        expect([ascending, [...ascending].reverse()]).toContainEqual(once);
        expect(twice).toEqual([...once].reverse());
        await tableSearch(page, 'benefiter_referrals_history').fill('no such text at all');
        await expect(page.locator('#benefiter_referrals_history tbody tr:has(td:nth-child(2))')).toHaveCount(0);
    });

    test('the update history pages by ten', async ({ page }) => {
        runPhp('\\DB::table("basic_folder_update_history")->insert(array_map(fn () => ["benefiter_id" => 2, "user_id" => 1, "medical_location_id" => 1, "update_date" => "2024-01-01", "comments" => "e2e page"], range(1, 12)));');
        await page.goto('/benefiter/2/basic-info');
        const rows = page.locator('#folders-update-history tbody tr');
        await expect(rows).toHaveCount(10);
        const firstPage = await rows.first().innerText();
        await tableNextPage(page, 'folders-update-history').click();
        await expect(rows.first()).not.toHaveText(firstPage);
    });
});

test.describe('dialogs', () => {
    test('a beneficiary is deleted after confirmation', async ({ page }) => {
        const path = await createBenefiter(page, `Delete-${unique()}`);
        const id = path.split('/')[2];
        await page.goto('/benefiters-list');
        const modal = page.locator('#delete-benefiter-modal');
        const trash = page.locator(`.delete-benefiter[data-url$="/benefiter/${id}/delete"]`);
        await trash.click();
        await expect(modal).toBeVisible();
        await modal.locator('.close').click();
        await expect(modal).toBeHidden();
        await trash.click();
        await expect(modal).toBeVisible();
        await page.keyboard.press('Escape');
        await expect(modal).toBeHidden();
        await trash.click();
        await expect(modal.locator('form')).toHaveAttribute('action', new RegExp(`/benefiter/${id}/delete$`));
        await modal.locator('button[type=submit]').click();
        await expect(page).toHaveURL(/\/benefiters-list$/);
        await expect(page.locator(`.delete-benefiter[data-url$="/benefiter/${id}/delete"]`)).toHaveCount(0);
    });

    test('a past medical visit opens and closes in a window', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        const modal = page.locator('#medicalHistory');
        await page.locator('.medical_visit_from_history').first().click();
        await expect(modal).toBeVisible();
        await expect(page.locator('#medical-visit-modal-content')).toContainText(/\S/);
        await modal.locator('.modal-content > .modal-footer button[data-dismiss=modal]').click();
        await expect(modal).toBeHidden();
        await page.locator('.medical_visit_from_history').nth(1).click();
        await expect(modal).toBeVisible();
        await modal.locator('.modal-header .close').click();
        await expect(modal).toBeHidden();
    });
});

test.describe('basic information', () => {
    test('the form of a new beneficiary warns about a possible duplicate', async ({ page }) => {
        await page.goto('/benefiter/-1/basic-info');
        await expect(page.locator('#duplicate-folders')).toBeHidden();
        await page.locator('input[name=lastname]').fill('Ben-lastname-1');
        await page.locator('input[name=name]').fill('Ben-name-1');
        await page.locator('input[name=name]').blur();
        await expect(page.locator('#duplicate-folders')).toBeVisible();
        await expect(page.locator('#duplicate-folders')).toContainText('Ben-name-1 Ben-lastname-1');
        await expect(page.locator('#duplicate-folders a').first()).toHaveAttribute('href', /\/benefiter\/1\/basic-info$/);
        await page.locator('input[name=lastname]').fill('Nobody-at-all');
        await page.locator('input[name=lastname]').blur();
        await expect(page.locator('#duplicate-folders')).toBeHidden();
    });

    test('a new beneficiary is saved, with languages and work', async ({ page }) => {
        const name = `Saved-${unique()}`;
        await page.goto('/benefiter/-1/basic-info');
        await page.locator('input[name=folder_number]').fill(`E2E-${unique()}`);
        await page.locator('input[name=lastname]').fill(`${name}-lastname`);
        await page.locator('input[name=name]').fill(name);
        await pickDay(page, page.locator('input[name=birth_date]'), 15);
        await expect(page.locator('input[name=birth_date]')).toHaveValue(/^15-\d{2}-\d{4}$/);
        await page.locator('input[name=birth_date]').locator('xpath=following-sibling::a[1]').locator('.clear-date').click();
        await expect(page.locator('input[name=birth_date]')).toHaveValue('');
        await page.locator('input[name=birth_date]').fill('03-04-1990');

        const languages = page.locator('#language-wrapper .added-div');
        const before = await languages.count();
        await page.locator('#language-wrapper .language-div .add-lang').click();
        await expect(languages).toHaveCount(before + 1);
        await expect(languages.last().locator('.remove-lang')).toBeVisible();
        await expect(languages.last().locator('.add-lang')).toBeHidden();
        await page.locator('#language-wrapper .language-div .add-lang').click();
        await expect(languages).toHaveCount(before + 2);
        await languages.last().locator('.remove-lang').click();
        await expect(languages).toHaveCount(before + 1);

        await page.locator('#hide_work_legally').check();
        await expect(page.locator('#working_legally_div')).toBeHidden();
        await expect(page.locator('#working_title_div')).toBeHidden();
        await page.locator('#show_work_legally').check();
        await expect(page.locator('#working_legally_div')).toBeVisible();
        await expect(page.locator('#working_title_div')).toBeVisible();

        await page.locator('#detention-date').fill('01-01-2020');
        await page.locator('#detention-date').blur();
        await expect(page.locator('#months-passed')).toHaveText(/^\(\d+ \S+\)$/);

        await page.locator('input[name=updated_by_date]').fill('01-02-2024');
        await page.locator('form input[type=submit].submit-button').last().click();
        await expect(page).toHaveURL(/\/benefiter\/\d+\/basic-info/);
        await expect(page.locator('.alert-success')).toBeVisible();
        await expect(page.locator('input[name=name]')).toHaveValue(name);
        await expect(page.locator('input[name=birth_date]')).toHaveValue('03-04-1990');
    });

    test('an incident is added, changed and deleted without leaving the page', async ({ page }) => {
        const path = await createBenefiter(page, `Incident-${unique()}`);
        await page.goto(path);
        await expect(page.locator('.new-occurrence').first()).toBeHidden();
        await page.locator('#add-new-occurrence').click();
        const form = page.locator('.new-occurrence:visible');
        await expect(form).toBeVisible();
        await form.locator('#occurrence_date').fill('05-06-2024');
        await form.locator('#occurrences_comments').fill('First incident text');
        await form.locator('.new-occurrence-submit').click();
        await expect(page.locator('.div-table-row', { hasText: 'First incident text' })).toBeVisible();

        await page.locator('.edit-occurrence').first().click();
        const edit = page.locator('.edit-occurrence-div:visible');
        await expect(edit).toBeVisible();
        await edit.locator('textarea').fill('Changed incident text');
        await edit.locator('.edit-occurrence-submit').click();
        await expect(page.locator('.div-table-row', { hasText: 'Changed incident text' })).toBeVisible();

        const modal = page.locator('.delete-occurrence-modal').first();
        await page.locator('.delete-occurrence').first().click();
        await expect(modal).toBeVisible();
        await modal.locator('.close').click();
        await expect(modal).toBeHidden();
        await page.locator('.delete-occurrence').first().click();
        await modal.locator('.delete-occurrence-confirm-button').click();
        await expect(page.locator('.delete-occurrence')).toHaveCount(0);
    });

    test('a referral is added and deleted after confirmation', async ({ page }) => {
        const path = await createBenefiter(page, `Referral-${unique()}`);
        await page.goto(path);
        const rows = page.locator('#basic_info_referrals > div');
        await expect(rows).toHaveCount(1);
        await page.locator('#basic_info_referrals .add-ref').first().click();
        await expect(rows).toHaveCount(2);
        await rows.last().locator('.remove-ref').click();
        await expect(rows).toHaveCount(1);
        await rows.first().locator('select').first().selectOption({ index: 1 });
        await rows.first().locator('input[name="basic_info_referrals_date[]"]').fill('07-08-2024');
        await page.locator('.add-new-referral').locator('xpath=following::input[@type="submit"][1]').click();
        await expect(page.locator('#benefiter_referrals_history tbody .delete-session')).toHaveCount(1);
        const modal = page.locator('#delete-session-modal');
        await page.locator('#benefiter_referrals_history .delete-session').first().click();
        await expect(modal).toBeVisible();
        await modal.locator('button[type=submit]').click();
        await expect(page.locator('#benefiter_referrals_history tbody .delete-session')).toHaveCount(0);
    });
});

test.describe('medical visit form', () => {
    test('the form opens, and rows are added and removed', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        const form = page.locator('#new-medical-visit');
        await expect(form).toBeHidden();
        await page.locator('#new-med-visit-button').click();
        await expect(form).toBeVisible();

        const groups: [string, string, string, string][] = [
            ['#chronic-cond', '.add-condition', '.remove-condition', 'input[name="chronic_conditions[]"]'],
            ['#lab-result', '.add-lab-result', '.remove-lab-result', 'textarea[name="lab_results[]"]'],
            ['#diagnosis-result', '.add-diagnosis-result', '.remove-diagnosis-result', 'textarea[name="diagnosis_results[]"]'],
            ['#hospitalization', '.add-hospitalization', '.remove-hospitalization', 'textarea[name="hospitalization[]"]'],
            ['#medication', '.add-med', '.remove-med', 'input[name="medication_dosage[]"]'],
            ['#referrals', '.add-ref', '.remove-ref', 'input[name="referrals[]"]'],
            ['#upload_file', '.add-file', '.remove-file', 'input[name="upload_file_description[]"]'],
        ];
        for (const [group, add, remove, field] of groups) {
            const fields = form.locator(`${group} ${field}`);
            await fields.first().fill('first value');
            await form.locator(`${group} ${add}`).first().click();
            await expect(fields, `${group} gets a row`).toHaveCount(2);
            await expect(fields.last(), `${group} new row is empty`).toHaveValue('');
            await form.locator(`${group} ${add}`).first().click();
            await expect(fields).toHaveCount(3);
            await form.locator(`${group} ${remove}:visible`).last().click();
            await expect(fields, `${group} loses a row`).toHaveCount(2);
            await expect(fields.first()).toHaveValue('first value');
        }
        await expect(form.locator('#medication select')).toHaveCount(2);
        await expect(form.locator('#medication input[name="medication_new_name[]"]').last()).toBeVisible();
    });

    test('the medicine list suggests medicines and hides the free text field', async ({ page }) => {
        await page.route('**/benefiter/getMedicationList**', (route) => route.fulfill({
            json: [{ id: 101, description: 'ASPIRIN 100MG TAB' }, { id: 102, description: 'ASPIRIN 500MG TAB' }],
        }));
        await page.goto('/benefiter/1/medical-folder');
        await page.locator('#new-med-visit-button').click();
        const row = page.locator('#medication .medicationList');
        const select = row.locator('select[name="medication_name_from_lookup[]"]');
        await expect(row.locator('input[name="medication_new_name[]"]')).toBeVisible();
        await chooseRemoteOption(page, select, 'asp', /ASPIRIN 500MG/);
        await expect(select).toHaveValue('102');
        await expect(row.locator('input[name="medication_new_name[]"]')).toBeHidden();

        await page.locator('#medication .add-med').first().click();
        const added = page.locator('#medication .med-added-div').last();
        const addedSelect = added.locator('select[name="medication_name_from_lookup[]"]');
        await expect(added.locator('input[name="medication_new_name[]"]')).toBeVisible();
        await chooseRemoteOption(page, addedSelect, 'asp', /ASPIRIN 100MG/);
        await expect(addedSelect).toHaveValue('101');
        await expect(select).toHaveValue('102');
        await expect(added.locator('input[name="medication_new_name[]"]')).toBeHidden();
    });

    test('the clinical results choose ICD-10 conditions', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        await page.locator('#new-med-visit-button').click();
        const select = page.locator('#clinical-select-0');
        const description = select.locator('xpath=following-sibling::textarea[1]');
        await expect(description).toBeHidden();
        await chooseRemoteOption(page, select, 'A00.1', /A00\.1/);
        await expect(description).toBeVisible();
        expect(await select.evaluate((element: HTMLSelectElement) => [...element.selectedOptions].map((option) => option.value))).toContain('2');
    });

    test('the medicine supply checkbox sets its hidden field', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        await page.locator('#new-med-visit-button').click();
        const row = page.locator('#medication .medicationList');
        await row.locator('.supply_from_praksis').check();
        await expect(row.locator('.supply_from_praksis_hidden')).toHaveValue('1');
        await row.locator('.supply_from_praksis').uncheck();
        await expect(row.locator('.supply_from_praksis_hidden')).toHaveValue('0');
    });

    test('files bigger than 50 MB in total stop the form', async ({ page }, testInfo) => {
        const big = testInfo.outputPath('big.pdf');
        mkdirSync(dirname(big), { recursive: true });
        writeFileSync(big, '');
        truncateSync(big, 51 * 1024 * 1024);
        await page.goto('/benefiter/1/medical-folder');
        await page.locator('#new-med-visit-button').click();
        await page.locator('#upload_file input[type=file]').first().setInputFiles(big);
        let message = '';
        page.once('dialog', (dialog) => { message = dialog.message(); void dialog.dismiss(); });
        await page.locator('#medical_visit_submit input[type=submit]').click();
        await expect.poll(() => message).toMatch(/\S/);
        await expect(page).toHaveURL(/medical-folder$/);
    });

    test('a medical visit is saved with a date from the date picker', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        const before = await page.locator('#benefiter_referrals_history tbody tr').count();
        await page.locator('#new-med-visit-button').click();
        await pickDay(page, page.locator('#new-medical-visit input[name=examination_date]'), 10);
        await expect(page.locator('#new-medical-visit input[name=examination_date]')).toHaveValue(/^10-\d{2}-\d{4}$/);
        await page.locator('#new-medical-visit input[name="chronic_conditions[]"]').first().fill('e2e condition');
        // The server needs a file with each new visit.
        await page.locator('#upload_file input[type=file]').first().setInputFiles({ name: 'e2e.pdf', mimeType: 'application/pdf', buffer: Buffer.from('%PDF-1.4\n%%EOF\n') });
        await page.locator('#medical_visit_submit input[type=submit]').click();
        await expect(page.locator('.alert-success')).toBeVisible();
        await expect(page.locator('#benefiter_referrals_history tbody tr')).toHaveCount(before + 1);
    });
});

test.describe('legal folder', () => {
    test('the sections follow the chosen answers', async ({ page }) => {
        await page.goto('/benefiter/3/legal-folder');
        await page.locator('#asylum').check();
        await expect(page.locator('.asylum-request').first()).toBeVisible();
        await expect(page.locator('.legal-status-div').first()).toBeHidden();
        await page.locator('#procedure-new').check();
        await expect(page.locator('.request-status').first()).toBeVisible();
        await page.locator('#procedure-old').check();
        await expect(page.locator('.request-status').first()).toBeHidden();

        await page.locator('#no-legal').check();
        await expect(page.locator('.asylum-request').first()).toBeHidden();
        await expect(page.locator('.legal-status-div').first()).toBeVisible();
        await page.locator('#action-refusal').check();
        await expect(page.locator('.results').first()).toBeVisible();
        await page.locator('#action-none').check();
        await expect(page.locator('.results').first()).toBeHidden();

        await page.locator('#penalty-yes').check();
        await expect(page.locator('.penalty-text').first()).toBeVisible();
        await page.locator('#penalty-no').check();
        await expect(page.locator('.penalty-text').first()).toBeHidden();
    });

    test('a lawyer session is added', async ({ page }) => {
        const path = await createBenefiter(page, `Legal-${unique()}`);
        await page.goto(path.replace('basic-info', 'legal-folder'));
        await page.locator('#asylum').check();
        await page.locator('form input[type=submit]').first().click();
        await expect(page.locator('#add-new-session')).toBeVisible();
        const form = page.locator('.new-session').first();
        await expect(form).toBeHidden();
        await page.locator('#add-new-session').click();
        await expect(form).toBeVisible();
        await form.locator('input[type=checkbox]').first().check();
        await form.locator('input[type=submit]').click();
        await expect(page.locator('#legal-sessions-history tbody tr').first()).toBeVisible();
        await expect(tableSearch(page, 'legal-sessions-history')).toBeVisible();
    });
});

test.describe('psychosocial folder', () => {
    test('a session is added, changed and deleted', async ({ page }) => {
        const path = await createBenefiter(page, `Session-${unique()}`);
        const folder = path.replace('basic-info', 'social-folder');
        await page.goto(folder);
        const form = page.locator('.new-session');
        await expect(form).toBeHidden();
        await page.locator('#add-new-session').click();
        await expect(form).toBeVisible();
        await pickDay(page, form.locator('input[name=session_date]'), 12);
        await expect(form.locator('input[name=session_date]')).toHaveValue(/^12-\d{2}-\d{4}$/);
        await form.locator('textarea[name=session_comments]').fill('First session text');
        await form.locator('input[type=submit]').click();
        await expect(page.locator('.div-table-row', { hasText: 'First session text' })).toBeVisible();

        await page.locator('.edit-session').first().click();
        const edit = page.locator('.edit-session-div').first();
        await expect(edit).toBeVisible();
        await edit.locator('textarea[name=session_comments]').fill('Changed session text');
        await edit.locator('input[type=submit]').click();
        await expect(page.locator('.div-table-row', { hasText: 'Changed session text' })).toBeVisible();

        const modal = page.locator('#delete-session-modal');
        await page.locator('.delete-session').first().click();
        await expect(modal).toBeVisible();
        await expect(modal.locator('form')).toHaveAttribute('action', /\/session-delete\/\d+$/);
        await modal.locator('button[type=submit]').click();
        await expect(page.locator('.div-table-row', { hasText: 'Changed session text' })).toHaveCount(0);
    });
});

test.describe('search', () => {
    test('the search shows the results and the button shows all beneficiaries', async ({ page }) => {
        await page.goto('/search');
        await expect(page.locator('#search-results')).toBeHidden();
        await page.locator('#search-form input[name=lastname]').fill('Ben-lastname-3');
        await page.locator('#search-btn').click();
        await expect(page.locator('#search-results')).toBeVisible();
        const rows = page.locator('#results tbody tr');
        await expect(rows).toHaveCount(1);
        await expect(rows.first()).toContainText('Ben-name-3');
        await expect(rows.first().locator('a')).toHaveAttribute('href', /\/benefiter\/\d+\/basic-info$/);
        await expect(page.locator('.state-loading')).toBeHidden();

        await page.locator('#fetch-all-btn').click();
        const total = Number(runPhp('echo \\DB::table("benefiters")->whereNull("deleted_at")->count();').trim());
        await expect(rows).toHaveCount(total);

        await page.locator('#search-form input[name=lastname]').fill('Nobody-at-all');
        await page.locator('#fetch-all-btn').locator('xpath=following::input[@id="search-btn"]').click();
        await expect(page.locator('.state-no-results')).toBeVisible();
    });

    test('the results download as a CSV file', async ({ page }) => {
        await page.goto('/search');
        await page.locator('#fetch-all-btn').click();
        await expect(page.locator('#results tbody tr').first()).toBeVisible();
        const [download] = await Promise.all([page.waitForEvent('download'), page.locator('#download-link').click()]);
        expect(download.suggestedFilename()).toMatch(/_Benefile_export\.csv$/);
        const content = await (await download.createReadStream()).toArray();
        const text = Buffer.concat(content).toString('utf8');
        expect(text).toContain('"KK1"');
    });

    test('the folder number of the side menu opens the search with its results', async ({ page }) => {
        await page.goto('/benefiters-list');
        await page.locator('#search-bar input[name=search]').fill('KK2');
        await page.locator('#search-bar button[type=submit]').click();
        await expect(page.locator('#results tbody tr')).toHaveCount(1);
        await expect(page.locator('#results tbody tr').first()).toContainText('Ben-lastname-2');
    });

    test('the date fields use the date picker', async ({ page }) => {
        await page.goto('/search');
        const input = page.locator('#search-form .date-input').first();
        await pickDay(page, input, 20);
        await expect(input).toHaveValue(/^20-\d{2}-\d{4}$/);
    });
});

test.describe('reports', () => {
    test('every chart draws', async ({ page }) => {
        await page.goto('/reports');
        for (const chart of ['#maritalStatusReport', '#medicalStatusReport', '#benefiters-work-title-chart', '#benefiters-per-medical-visits-chart',
            '#ageReport', '#legalStatusReport', '#registrations_per_month', '#benefiter_vs_education', '#benefiter_vs_doctor_type',
            '#benefiter_vs_clinical_conditions', '#medical_visits_per_month', '#benefiter_vs_phycological_support']) {
            await expect(page.locator(`${chart} svg`).first(), `${chart} draws`).toBeVisible();
        }
    });
});

test.describe('CSV import', () => {
    test('a file is uploaded and the result shows', async ({ page }) => {
        await page.goto('/new-benefiter/uploadCSV');
        await expect(page.locator('.success-message')).toBeHidden();
        await page.locator('input.dz-hidden-input').setInputFiles({ name: 'e2e.csv', mimeType: 'text/csv', buffer: Buffer.from('folder_number;name\n') });
        await expect(page.locator('.success-message:visible, #error-list li').first()).toBeVisible();
        await expect(page.locator('.dz-preview')).toHaveCount(1);
    });
});

test.describe('users', () => {
    test('an administrator deactivates and activates a user', async ({ page }) => {
        await page.goto('/main-panel/users-list');
        await page.locator('#active').click();
        const row = page.locator('#usersTable-active tbody tr', { hasText: 'e2e-social@example.test' });
        await row.locator('button').click();
        await page.locator('#inactive').click();
        const inactive = page.locator('#usersTable-deactivated tbody tr', { hasText: 'e2e-social@example.test' });
        await expect(inactive).toHaveCount(1);
        await inactive.locator('button').click();
        await page.locator('#active').click();
        await expect(page.locator('#usersTable-active tbody tr', { hasText: 'e2e-social@example.test' })).toHaveCount(1);
    });
});

test('no page of this file has a JavaScript error while it is used', async ({ page }) => {
    const errors = watchErrors(page);
    await page.goto('/benefiter/1/medical-folder');
    await page.locator('#new-med-visit-button').click();
    await page.locator('#medication .add-med').first().click();
    await page.goto('/search');
    await page.locator('#fetch-all-btn').click();
    await expect(page.locator('#results tbody tr').first()).toBeVisible();
    expect(errors).toEqual([]);
});
