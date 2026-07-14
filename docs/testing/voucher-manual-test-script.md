# Skrip Ujian Manual — Fungsi Voucher

**Modul:** Voucher (`/voucher`)
**URL asas:** `http://ezjual.test`
**Akaun ujian:** sofia.itmin@gmail.com / password

## Sebelum mula
- [ ] Pastikan sekurang-kurangnya satu produk wujud dalam sistem (Produk > Senarai Produk).
- [ ] Catat kod voucher sedia ada (contoh: `FREE`) supaya tidak tertimpa semasa ujian.

---

## 1. Log Masuk
| # | Langkah | Jangkaan |
|---|---------|----------|
| 1.1 | Buka `http://ezjual.test/login` | Halaman log masuk dipaparkan |
| 1.2 | Masukkan e-mel `sofia.itmin@gmail.com` dan kata laluan `password`, klik **Log Masuk** | Berjaya log masuk dan dibawa ke `/dashboard` |

- [ ] Lulus

---

## 2. Senarai Voucher
| # | Langkah | Jangkaan |
|---|---------|----------|
| 2.1 | Klik pautan **Voucher** pada navigasi kiri | Dibawa ke `/voucher` |
| 2.2 | Perhatikan jadual | Tajuk "Voucher", subtajuk "Urus kod diskaun untuk produk anda", butang **Tambah Voucher**, dan lajur Kod / Diskaun / Produk / Luput / Status / Tindakan dipaparkan |

- [ ] Lulus

---

## 3. Tambah Voucher — Pengesahan Medan Wajib
| # | Langkah | Jangkaan |
|---|---------|----------|
| 3.1 | Klik **Tambah Voucher** | Dibawa ke `/voucher/baru` |
| 3.2 | Jangan isi apa-apa. Klik terus **Simpan Voucher** | Borang tidak dihantar; mesej pelayar "Please fill out this field" muncul pada medan **Kod Voucher**; masih di `/voucher/baru` |

- [ ] Lulus

---

## 4. Tambah Voucher — Data Sah
| # | Langkah | Jangkaan |
|---|---------|----------|
| 4.1 | Isi **Kod Voucher**: `QATEST01` | — |
| 4.2 | Biarkan **Jenis Diskaun** = Peratusan (%) | — |
| 4.3 | Isi **Nilai Diskaun**: `15` | — |
| 4.4 | Tandakan satu produk di bawah **Produk Berkenaan** | — |
| 4.5 | Klik **Simpan Voucher** | Dibawa semula ke `/voucher`; baris baharu `QATEST01`, `15.00%`, produk yang dipilih, status **Aktif** dipaparkan |

- [ ] Lulus

---

## 5. Aktifkan / Nyahaktifkan Voucher
| # | Langkah | Jangkaan |
|---|---------|----------|
| 5.1 | Pada baris `QATEST01`, klik **Nyahaktifkan** | Status bertukar kepada **Tidak Aktif** serta-merta (tiada reload halaman) |
| 5.2 | Klik **Aktifkan** pada baris yang sama | Status bertukar semula kepada **Aktif** |

- [ ] Lulus

---

## 6. Edit Voucher (tanpa tukar kod)
| # | Langkah | Jangkaan |
|---|---------|----------|
| 6.1 | Klik **Edit** pada baris `QATEST01` | Dibawa ke `/voucher/{id}/edit`; semua medan pra-isi mengikut data sedia ada |
| 6.2 | Tukar **Nilai Diskaun** sahaja kepada `20`, kod dibiarkan tidak berubah | — |
| 6.3 | Klik **Kemaskini Voucher** | Tiada ralat "code sudah digunakan"; kemaskinian berjaya (toast "Voucher dikemaskini." dipaparkan) |
| 6.4 | Kembali ke `/voucher`, sahkan nilai baharu `20.00%` dipaparkan | Berjaya |

- [ ] Lulus

---

## 7. Kod Voucher Pendua (kes huruf berbeza)
> Ujian ini mengesahkan pembetulan pepijat: kod yang sama tetapi huruf besar/kecil berbeza (contoh `qatest01` vs `QATEST01`) mesti ditolak dengan **mesej pengesahan**, bukan ranap sistem (500 error).

| # | Langkah | Jangkaan |
|---|---------|----------|
| 7.1 | Klik **Tambah Voucher** | Dibawa ke `/voucher/baru` |
| 7.2 | Isi **Kod Voucher**: `qatest01` (huruf kecil, sama seperti voucher sedia ada tetapi kes berbeza) | — |
| 7.3 | Isi **Nilai Diskaun**: `5`, tandakan mana-mana produk | — |
| 7.4 | Klik **Simpan Voucher** | Mesej ralat **"code sudah digunakan."** dipaparkan di bawah medan Kod Voucher; **TIADA** halaman ralat "Internal Server Error" / 500 |

- [ ] Lulus

---

## 8. Padam Voucher
| # | Langkah | Jangkaan |
|---|---------|----------|
| 8.1 | Pada baris `QATEST01`, klik **Padam** | Dialog pengesahan pelayar "Padam voucher ini?" muncul |
| 8.2 | Klik **OK/Sahkan** | Baris `QATEST01` hilang dari jadual; toast "Voucher dipadam." dipaparkan |

- [ ] Lulus

---

## Pembersihan Selepas Ujian
- [ ] Sahkan tiada baki voucher ujian (`QATEST01`, `qatest01`, dsb.) dalam senarai — hanya voucher asal (contoh `FREE`) yang patut kekal.

---

## Ringkasan Keputusan

| Bahagian | Status | Catatan |
|----------|--------|---------|
| 1. Log Masuk | ☐ Lulus / ☐ Gagal | |
| 2. Senarai Voucher | ☐ Lulus / ☐ Gagal | |
| 3. Pengesahan Medan Wajib | ☐ Lulus / ☐ Gagal | |
| 4. Tambah Voucher (Sah) | ☐ Lulus / ☐ Gagal | |
| 5. Aktif/Nyahaktif | ☐ Lulus / ☐ Gagal | |
| 6. Edit Voucher | ☐ Lulus / ☐ Gagal | |
| 7. Kod Pendua (Huruf Berbeza) | ☐ Lulus / ☐ Gagal | |
| 8. Padam Voucher | ☐ Lulus / ☐ Gagal | |

**Diuji oleh:** _______________
**Tarikh:** _______________
