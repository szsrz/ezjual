# Ujian Browser (Playwright)

Ujian end-to-end automatik untuk aplikasi ini menggunakan [Playwright](https://playwright.dev/). Fail ujian berada dalam folder `e2e/`.

## Setup (sekali sahaja)

```
npm install
npx playwright install chromium
```

## Jalankan Ujian

```
# Jalankan semua ujian (headless, default)
npx playwright test

# Jalankan dengan browser fizikal dipaparkan (headed)
npx playwright test --headed

# Jalankan satu fail ujian sahaja
npx playwright test e2e/voucher.spec.ts

# Jalankan mengikut nama ujian (guna -g / grep)
npx playwright test -g "toggling active status"

# Mod UI interaktif (debug step-by-step)
npx playwright test --ui

# Mod debug (buka Playwright Inspector)
npx playwright test --debug
```

## Lihat Laporan

Selepas ujian dijalankan, laporan HTML dijana secara automatik:

```
npx playwright show-report
```

## Konfigurasi

- `playwright.config.ts` — URL asas (`http://ezjual.test`), projek `setup` (log masuk) dan `chromium`.
- `e2e/auth.setup.ts` — log masuk sekali dan simpan sesi ke `e2e/.auth/user.json` untuk digunakan oleh semua ujian lain.
- `e2e/voucher.spec.ts` — senario ujian modul Voucher (senarai, tambah, pengesahan, toggle aktif, edit, kod pendua, padam).

## Override Kredensial Ujian

Secara lalai, `e2e/auth.setup.ts` log masuk menggunakan `sofia.itmin@gmail.com` / `password`. Boleh override melalui environment variable:

```
TEST_USER_EMAIL=lain@contoh.com TEST_USER_PASSWORD=lainpassword npx playwright test
```

## Nota

- Setiap ujian membersihkan data yang ia cipta sendiri (padam voucher ujian selepas selesai) — jangan risau data sampah tertinggal dalam pangkalan data selepas ujian lulus.
- Fail sesi (`e2e/.auth/`) dan laporan (`playwright-report/`, `test-results/`) tidak disimpan dalam git (lihat `.gitignore`).
