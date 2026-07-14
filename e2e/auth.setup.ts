import { test as setup } from '@playwright/test';

const authFile = 'e2e/.auth/user.json';

setup('authenticate', async ({ page }) => {
    await page.goto('/login');

    await page.getByRole('textbox', { name: 'Alamat e-mel' }).fill(process.env.TEST_USER_EMAIL ?? 'sofia.itmin@gmail.com');
    await page.getByRole('textbox', { name: 'Kata Laluan' }).fill(process.env.TEST_USER_PASSWORD ?? 'password');
    await page.getByRole('button', { name: 'Log Masuk' }).click();

    await page.waitForURL('**/dashboard');

    await page.context().storageState({ path: authFile });
});
