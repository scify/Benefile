import { defineConfig } from '@playwright/test';

/**
 * Browser tests of every screen. They run against a local installation
 * (BASE_URL, by default the DDEV site), in the Google Chrome of the computer.
 * See the README, section "Browser tests".
 */
export default defineConfig({
    testDir: './tests/e2e',
    globalSetup: './tests/e2e/global-setup.ts',
    fullyParallel: false,
    workers: 1,
    retries: 0,
    reporter: [['list'], ['html', { open: 'never', outputFolder: 'storage/playwright-report' }]],
    outputDir: 'storage/playwright-results',
    use: {
        baseURL: process.env.BASE_URL ?? 'https://benefile.ddev.site',
        channel: 'chrome',
        ignoreHTTPSErrors: true,
        locale: 'el-GR',
        viewport: { width: 1440, height: 900 },
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
    },
});
