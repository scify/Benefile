import { expect, test } from '@playwright/test';
import { authFile } from './global-setup';

/**
 * The interface is in Greek by default, and in English after the visitor chooses it.
 */
test.describe('language', () => {
    test('the login page switches to English and back', async ({ page }) => {
        await page.goto('/auth/login');
        await expect(page.locator('html')).toHaveAttribute('lang', 'el');
        await page.getByRole('link', { name: 'English' }).click();
        await expect(page.locator('html')).toHaveAttribute('lang', 'en');
        await expect(page.getByText('Forgot your password?', { exact: false })).toBeVisible();
        await page.getByRole('link', { name: 'Ελληνικά' }).click();
        await expect(page.locator('html')).toHaveAttribute('lang', 'el');
    });

    test.describe('in the application', () => {
        test.use({ storageState: authFile('admin') });

        for (const path of ['/benefiters-list', '/benefiter/1/basic-info', '/benefiter/1/medical-folder', '/benefiter/1/legal-folder', '/benefiter/1/social-folder', '/search', '/reports', '/main-panel/users-list']) {
            test(`${path} has no Greek interface text in English`, async ({ page }) => {
                await page.goto('/language/en');
                await page.goto(path);
                await expect(page.locator('html')).toHaveAttribute('lang', 'en');
                // Labels, headings, buttons and menu items: the data of the beneficiaries may stay Greek.
                const greek = await page.locator('label, h1, h2, h3, h4, th, button, .nav a, #main-menu a, .record-section-header').evaluateAll((elements) =>
                    elements.map((element) => (element as HTMLElement).innerText.trim()).filter((text) => /[Ͱ-Ͽ]/.test(text)));
                await page.screenshot({ path: `storage/playwright-screens/en${path.replaceAll('/', '-')}.png`, fullPage: true });
                expect(greek).toEqual([]);
            });
        }
    });
});
