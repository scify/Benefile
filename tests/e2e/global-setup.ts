import { chromium, type FullConfig } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import { randomBytes } from 'node:crypto';
import { mkdirSync } from 'node:fs';

export const roles = ['admin', 'doctor', 'lawyer', 'social', 'psychologist'] as const;
export type Role = (typeof roles)[number];
export const authFile = (role: Role) => `storage/playwright-auth/${role}.json`;

/**
 * Creates one user of each role with a new random password (BrowserTestUsersSeeder),
 * logs in as each one, and keeps the sessions for the tests.
 */
export default async function globalSetup(config: FullConfig): Promise<void> {
    const password = randomBytes(18).toString('base64url');
    const seed = process.env.E2E_SEED_COMMAND ?? 'ddev';
    const args = seed === 'ddev'
        ? ['exec', `E2E_PASSWORD=${password} php artisan db:seed --class=Database\\\\Seeders\\\\BrowserTestUsersSeeder --force`]
        : ['artisan', 'db:seed', '--class=Database\\Seeders\\BrowserTestUsersSeeder', '--force'];
    execFileSync(seed === 'ddev' ? 'ddev' : 'php', args, { stdio: 'inherit', env: { ...process.env, E2E_PASSWORD: password } });

    mkdirSync('storage/playwright-auth', { recursive: true });
    const { baseURL, ignoreHTTPSErrors, channel } = config.projects[0].use;
    const browser = await chromium.launch({ channel });
    for (const role of roles) {
        const page = await browser.newPage({ baseURL, ignoreHTTPSErrors });
        await page.goto('/auth/login');
        await page.fill('input[name=email]', `e2e-${role}@example.test`);
        await page.fill('input[name=password]', password);
        await Promise.all([page.waitForURL('**/benefiters-list'), page.click('form [type=submit], form button')]);
        await page.context().storageState({ path: authFile(role) });
        await page.close();
    }
    await browser.close();
}
