import { expect, test, type Page } from '@playwright/test';
import { authFile, type Role } from './global-setup';

/**
 * Opens every screen of BeneFile with the role that uses it. Each screen must
 * open, show its main content, and have no JavaScript error. A screenshot of
 * each screen goes to storage/playwright-screens/.
 */

// JavaScript errors that the screens had before the tests existed. Remove a line when its error is fixed.
const knownErrors: RegExp[] = [];

function watchErrors(page: Page): string[] {
    const errors: string[] = [];
    page.on('pageerror', (error) => errors.push(error.message));
    page.on('console', (message) => {
        if (message.type() === 'error' && !/favicon|Failed to load resource.*(fonts\.googleapis|cdnjs)/.test(message.text())) {
            errors.push(message.text());
        }
    });
    return errors;
}

async function openScreen(page: Page, path: string, name: string, mainContent: string): Promise<void> {
    const errors = watchErrors(page);
    const response = await page.goto(path);
    expect(response?.status(), `${path} answers`).toBeLessThan(400);
    await expect(page.locator(mainContent).first(), `${path} shows ${mainContent}`).toBeVisible();
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: `storage/playwright-screens/${name}.png`, fullPage: true });
    const unexpected = errors.filter((error) => !knownErrors.some((known) => known.test(error)));
    expect(unexpected, `${path} has no JavaScript error`).toEqual([]);
}

test.describe('screens without login', () => {
    test('login', async ({ page }) => openScreen(page, '/auth/login', 'login', 'input[name=password]'));
    test('registration', async ({ page }) => openScreen(page, '/auth/register', 'register', 'select[name=user_role_id]'));
    test('forgotten password', async ({ page }) => openScreen(page, '/password/email', 'password-email', 'input[name=email]'));
    test('a guest goes to the login page', async ({ page }) => {
        await page.goto('/benefiters-list');
        await expect(page).toHaveURL(/\/auth\/login$/);
    });
});

const asRole = (role: Role) => test.use({ storageState: authFile(role) });

test.describe('screens of the administrator', () => {
    asRole('admin');

    test('beneficiaries', async ({ page }) => openScreen(page, '/benefiters-list', 'benefiters-list', 'table'));
    test('new beneficiary', async ({ page }) => openScreen(page, '/benefiter/-1/basic-info', 'new-benefiter', 'input[name=lastname]'));
    test('basic information', async ({ page }) => openScreen(page, '/benefiter/1/basic-info', 'basic-info', 'input[name=lastname]'));
    test('medical folder', async ({ page }) => openScreen(page, '/benefiter/1/medical-folder', 'medical-folder', '.medical_visit_from_history'));
    test('legal folder', async ({ page }) => openScreen(page, '/benefiter/1/legal-folder', 'legal-folder', 'form'));
    test('psychosocial folder', async ({ page }) => openScreen(page, '/benefiter/1/social-folder', 'social-folder', 'form'));
    test('search', async ({ page }) => openScreen(page, '/search', 'search', '#search-form'));
    test('reports', async ({ page }) => openScreen(page, '/reports', 'reports', '#main-layout'));
    test('users', async ({ page }) => openScreen(page, '/main-panel/users-list', 'users', '#results-active, table'));
    test('places of examination', async ({ page }) => openScreen(page, '/new-location', 'new-location', 'form'));
    test('CSV import', async ({ page }) => openScreen(page, '/new-benefiter/uploadCSV', 'csv-import', '#main-layout'));

    test('a past medical visit opens in a window', async ({ page }) => {
        const errors = watchErrors(page);
        await page.goto('/benefiter/1/medical-folder');
        await page.locator('.medical_visit_from_history').first().click();
        await expect(page.locator('#medicalHistory')).toBeVisible();
        await expect(page.locator('#medicalHistory')).toContainText(/\S/);
        await page.screenshot({ path: 'storage/playwright-screens/medical-visit-window.png' });
        expect(errors.filter((error) => !knownErrors.some((known) => known.test(error)))).toEqual([]);
    });

    test('a past medical visit opens for editing', async ({ page }) => {
        await page.goto('/benefiter/1/medical-folder');
        const visitId = await page.locator('.medical_visit_from_history').first().getAttribute('value');
        await openScreen(page, `/benefiter/1/editMedicalVisit?medical_visit_id=${visitId}`, 'medical-visit-edit', 'form');
    });

    test('the search finds a beneficiary', async ({ page }) => {
        await page.goto('/search');
        // The page sends the search with JavaScript and shows the results below the form.
        await page.locator('#search-form input[name=name]').fill('Ben-name-1');
        await page.locator('#search-btn').click();
        await expect(page.getByText('Ben-lastname-1').first()).toBeVisible();
        await page.screenshot({ path: 'storage/playwright-screens/search-results.png', fullPage: true });
    });

    test('the users list shows the active users', async ({ page }) => {
        await page.goto('/main-panel/users-list');
        await page.locator('#active').click();
        await expect(page.locator('#results-active')).toContainText('e2e-doctor@example.test');
    });
});

test.describe('screens of the other roles', () => {
    for (const role of ['doctor', 'lawyer', 'social', 'psychologist'] as const) {
        test.describe(role, () => {
            asRole(role);

            test('reads every folder', async ({ page }) => {
                for (const folder of ['basic-info', 'medical-folder', 'legal-folder', 'social-folder']) {
                    const response = await page.goto(`/benefiter/1/${folder}`);
                    expect(response?.status(), `${role} opens ${folder}`).toBe(200);
                }
            });

            test('has no administration pages', async ({ page }) => {
                for (const path of ['/main-panel/users-list', '/new-location', '/new-benefiter/uploadCSV']) {
                    const response = await page.goto(path);
                    expect(response?.status(), `${role} on ${path}`).toBe(403);
                }
            });
        });
    }
});
