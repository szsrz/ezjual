import { test, expect } from '@playwright/test';

test.describe('Voucher management', () => {
    test('voucher list page shows the table and Tambah Voucher action', async ({ page }) => {
        await page.goto('/voucher');

        await expect(page.getByText('Urus kod diskaun untuk produk anda')).toBeVisible();
        await expect(page.getByRole('link', { name: 'Tambah Voucher' })).toBeVisible();
        await expect(page.getByRole('columnheader', { name: 'Kod' })).toBeVisible();
    });

    test('create form blocks submit when required fields are empty', async ({ page }) => {
        await page.goto('/voucher/baru');

        const codeInput = page.getByRole('textbox', { name: 'Kod Voucher' });
        await page.getByRole('button', { name: 'Simpan Voucher' }).click();

        await expect(page).toHaveURL(/voucher\/baru/);
        const isInvalid = await codeInput.evaluate((el: HTMLInputElement) => !el.checkValidity());
        expect(isInvalid).toBe(true);
    });

    test('creating a voucher with valid data succeeds and appears in the list', async ({ page }) => {
        const code = `QATEST${Date.now()}`;

        await page.goto('/voucher/baru');
        await page.getByRole('textbox', { name: 'Kod Voucher' }).fill(code);
        await page.getByRole('spinbutton', { name: 'Nilai Diskaun' }).fill('15');
        await page.getByRole('checkbox', { name: 'Air Fryer 5L Digital' }).check();
        await page.getByRole('button', { name: 'Simpan Voucher' }).click();

        await expect(page).toHaveURL(/\/voucher$/);
        const row = page.getByRole('row', { name: new RegExp(code) });
        await expect(row).toBeVisible();
        await expect(row).toContainText('15.00%');
        await expect(row).toContainText('Air Fryer 5L Digital');

        page.once('dialog', (dialog) => dialog.accept());
        await row.getByText('Padam').click();
        await expect(page.getByText('Voucher dipadam.')).toBeVisible();
    });

    test('toggling active status flips the badge immediately', async ({ page }) => {
        const code = `QATOGGLE${Date.now()}`;

        await page.goto('/voucher/baru');
        await page.getByRole('textbox', { name: 'Kod Voucher' }).fill(code);
        await page.getByRole('spinbutton', { name: 'Nilai Diskaun' }).fill('10');
        await page.getByRole('checkbox', { name: 'Air Fryer 5L Digital' }).check();
        await page.getByRole('button', { name: 'Simpan Voucher' }).click();

        const row = page.getByRole('row', { name: new RegExp(code) });
        await expect(row.getByText('Aktif', { exact: true })).toBeVisible();

        await row.getByText('Nyahaktifkan').click();
        await expect(row.getByText('Tidak Aktif')).toBeVisible();

        await row.getByText('Aktifkan').click();
        await expect(row.getByText('Aktif', { exact: true })).toBeVisible();

        page.once('dialog', (dialog) => dialog.accept());
        await row.getByText('Padam').click();
    });

    test('creating a voucher whose code differs only in case from an existing one is rejected with a validation message, not a server crash', async ({ page }) => {
        const base = `QADUPE${Date.now()}`;

        try {
            await page.goto('/voucher/baru');
            await page.getByRole('textbox', { name: 'Kod Voucher' }).fill(base);
            await page.getByRole('spinbutton', { name: 'Nilai Diskaun' }).fill('10');
            await page.getByRole('checkbox', { name: 'Air Fryer 5L Digital' }).check();
            await page.getByRole('button', { name: 'Simpan Voucher' }).click();
            await expect(page).toHaveURL(/\/voucher$/);

            await page.goto('/voucher/baru');
            await page.getByRole('textbox', { name: 'Kod Voucher' }).fill(base.toLowerCase());
            await page.getByRole('spinbutton', { name: 'Nilai Diskaun' }).fill('5');
            await page.getByRole('checkbox', { name: 'Baju Kurung Moden Sulam' }).check();
            await page.getByRole('button', { name: 'Simpan Voucher' }).click();

            await expect(page.getByText('sudah digunakan')).toBeVisible();
            await expect(page.getByText('Internal Server Error')).not.toBeVisible();
        } finally {
            await page.goto('/voucher');
            const row = page.getByRole('row', { name: new RegExp(base, 'i') });
            if (await row.count()) {
                page.once('dialog', (dialog) => dialog.accept());
                await row.getByText('Padam').click();
            }
        }
    });

    test('editing a voucher without changing its code saves successfully', async ({ page }) => {
        await page.goto('/voucher');
        const row = page.getByRole('row', { name: /FREE/ });
        await row.getByRole('link', { name: 'Edit' }).click();

        const valueInput = page.getByRole('spinbutton', { name: 'Nilai Diskaun' });
        const originalValue = await valueInput.inputValue();
        const newValue = '12';

        try {
            await valueInput.fill(newValue);
            await page.getByRole('button', { name: 'Kemaskini Voucher' }).click();

            await expect(page.getByText('code sudah digunakan')).not.toBeVisible();
            await expect(valueInput).toHaveValue(newValue);
        } finally {
            await valueInput.fill(originalValue);
            await page.getByRole('button', { name: 'Kemaskini Voucher' }).click();
        }
    });
});
