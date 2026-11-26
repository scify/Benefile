import { expect, type Locator, type Page } from '@playwright/test';
import { execFileSync } from 'node:child_process';

/**
 * Shared steps of the interaction tests.
 *
 * The steps that touch a library (tables, date pickers, lists with search)
 * are here, so that a test describes what the user does, not how a library draws it.
 */

/** Runs PHP code in the application (php artisan tinker), to prepare test data. */
export function runPhp(code: string): string {
    const ddev = (process.env.E2E_SEED_COMMAND ?? 'ddev') === 'ddev';
    const command = ddev ? 'ddev' : 'php';
    const args = ddev ? ['exec', 'php', 'artisan', 'tinker', `--execute=${code}`] : ['artisan', 'tinker', `--execute=${code}`];
    return execFileSync(command, args, { encoding: 'utf8' });
}

/** Collects the JavaScript errors of a page. */
export function watchErrors(page: Page): string[] {
    const errors: string[] = [];
    page.on('pageerror', (error) => errors.push(error.message));
    page.on('console', (message) => {
        if (message.type() === 'error' && !/favicon|Failed to load resource.*(fonts\.googleapis|cdnjs)/.test(message.text())) {
            errors.push(message.text());
        }
    });
    return errors;
}

/** The search field of a table with sorting, search and paging. */
export function tableSearch(page: Page, tableId: string): Locator {
    return page.locator(`#${tableId}`).locator('xpath=ancestor::div[contains(@class, "datatable-wrapper") or contains(@class, "dataTables_wrapper")][1]').locator('input[type=search]');
}

/** The button that shows the next page of a table. */
export function tableNextPage(page: Page, tableId: string): Locator {
    return page.locator(`#${tableId}`).locator('xpath=ancestor::div[contains(@class, "datatable-wrapper") or contains(@class, "dataTables_wrapper")][1]')
        .locator('a.paginate_button.next, .datatable-pagination [data-page]:has-text("›")').first();
}

/** The texts of one column of the visible rows of a table. */
export async function columnTexts(page: Page, tableId: string, column: number): Promise<string[]> {
    return page.locator(`#${tableId} tbody tr td:nth-child(${column})`).allInnerTexts();
}

/** Opens the date picker of a date field and chooses a day of the shown month. */
export async function pickDay(page: Page, input: Locator, day: number): Promise<void> {
    await input.click();
    const picker = page.locator('.datepicker:visible, .datepicker.active');
    await expect(picker.first()).toBeVisible();
    await picker.first().locator('td.day:not(.old):not(.new), .datepicker-cell.day:not(.prev):not(.next)')
        .getByText(String(day), { exact: true }).first().click();
    // A click outside closes the date picker, as the user does.
    await page.locator('#header, body').first().click({ position: { x: 5, y: 5 } });
}

/** Types into a list with search (a select with remote options) and chooses an option. */
export async function chooseRemoteOption(page: Page, select: Locator, text: string, option: RegExp): Promise<void> {
    const box = select.locator('xpath=following-sibling::*[contains(@class, "select2") or contains(@class, "ts-wrapper")][1]');
    await box.click();
    await page.locator('.select2-container--open .select2-search__field, .ts-wrapper.focus input').first().pressSequentially(text, { delay: 50 });
    await page.locator('.select2-results__option, .ts-dropdown .option').filter({ hasText: option }).first().click();
}
