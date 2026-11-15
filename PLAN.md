# PLAN — Kontribusi & Instalasi Offline

Status: rencana. Belum dieksekusi (kecuali import buku, lihat bawah).
Sasaran: memudahkan collaborator (campur, ada awam) + laptop Windows sekolah (offline, operator awam).

---



## 2. Onboarding collaborator (belum dieksekusi)

Keputusan: **tanpa CI** (hemat menit repo private). Validasi manual via `composer test` + `vendor/bin/pint`.

Deliverables:

1. `CONTRIBUTING.md` — setup (`composer setup`), branch model (jangan push `main`, PR), standar kode (`vendor/bin/pint`), gate test (`composer test`), gaya commit (pola repo: `laporan: ...`, `dashboard ...`), checklist PR.
2. `docs/Manual-Pengguna.md` — pakai app, backup/restore, reset password (`php artisan admin:reset-password`).
3. `docs/Instalasi-Windows-Offline.md` — panduan operator klik-demi-klik, tanpa jargon.
4. `.github/PULL_REQUEST_TEMPLATE.md` + `.github/ISSUE_TEMPLATE.md`.
5. Fix `README.md` — hapus rujukan `docs/02-DPPL.md` (file tak ada), link ke 2 dokumen baru.
6. Rapikan `.env.example` — un-comment blok DB, `APP_LOCALE=id` (sekarang `en`).

Catatan: `docs/02-DPPL.md` **dibuang** (diminta). Hanya 2 dokumen.

---

## 3. Instalasi offline laptop Windows sekolah (belum dieksekusi)

Keputusan:
- **Laragon dibuang** — GUI-nya menampilkan MySQL/port/console (bocor konsep DB) ke operator awam.
- Ganti **installer custom** (Inno Setup) + **service MariaDB mode Manual** + **launcher EXE**.
- Admin rights: **ada**, tapi **tanpa auto-start** (operator nyalakan manual tiap hari).

Target pengalaman operator (nol konsep DB):
1. Sekali: colok USB → `SIMPUS-Install.exe` → Next → Selesai (klik "Yes" izin admin sekali).
2. Muncul 3 ikon desktop: **SIMPUS Perpustakaan**, **Backup SIMPUS**, **Tutup SIMPUS**.
3. Tiap hari: double-click **SIMPUS Perpustakaan** → browser buka sendiri → pakai.
4. Selesai: tutup browser / klik **Tutup SIMPUS** (atau matikan laptop, aman — InnoDB pulih).
5. Backup: klik **Backup SIMPUS** → file tersimpan otomatis (timestamp).

Teknis (disembunyikan dari operator):
- MariaDB 10.6+ sebagai Windows service mode **Manual** (bukan auto).
- App (`php artisan serve`) + DB start diam-diam, browser buka setelah port siap.
- Ekstensi PHP wajib: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `fileinfo` (dompdf).

Deliverables:
1. `scripts/build-installer` — `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, set `.env` produksi (`APP_ENV=production`, `APP_DEBUG=false`), `key:generate`, `migrate --seed`, bungkus jadi `SIMPUS-Install.exe` (Inno Setup).
2. `scripts/launcher/` — source launcher EXE (start service + buka browser), script Backup & Tutup.
3. `docs/Instalasi-Windows-Offline.md` — klik-demi-klik.

Perhatian:
- `vendor/` & `public/build` = artifact installer, **tidak** di-commit (sudah gitignore).
- `.env` produksi (berisi APP_KEY) = bagian installer, bukan di git.
- Seeder saat ini belum seed `kategori` → diisi otomatis lewat import buku (poin 1).

---

## Urutan eksekusi yang disarankan


2. Dokumen kontribusi (poin 2) — cepat, langsung bermanfaat.
4. Build installer + launcher (poin 3) — paling berat, terakhir.
