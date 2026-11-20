# Project Memory – SIFANTAR Blade (Laravel 13)

## Stack & Lingkungan
- Laravel 13.8, PHP 8.3, PostgreSQL lokal (`DB_DATABASE=sifantar_new`, user `postgres`).
- Frontend: Blade + Tailwind via CDN (`@tailwindcss/browser@4`) + Lucide icons. Layout tunggal: `resources/views/layouts/app.blade.php` (styling wajib ikut kelas `btn-primary`, `input-field`, `card-sifantar`, warna `primary-green`/`primary-orange`).
- `.env` lokal memakai `MAIL_MAILER=log` → email verifikasi/reset masuk ke `storage/logs/laravel.log`.

## Arsitektur Routing
- `routes/web.php` adalah SATU-SATUNYA file route (tidak ada `auth.php`).
- Login/register memakai view lewat catch-all `Route::get('/{page}')` name `page` → `pages/index` (login) & `pages/register`.
- Middleware alias: `admin`, `courier` (didaftarkan di `bootstrap/app.php`).

## Log Keputusan & Catatan Khusus
- **2026-11-20 — Verifikasi Email (MustVerifyEmail)**: `App\Models\User` meng-implements `Illuminate\Contracts\Auth\MustVerifyEmail`. Handler ada di `app/Http/Controllers/Auth/EmailVerificationController.php` (notice/verify/send) dengan route `verification.notice`, `verification.verify` (middleware `signed` + `throttle:6,1`), `verification.send`. View: `pages/verify-email.blade.php`.
  - Register → kirim `sendEmailVerificationNotification()` → redirect `verification.notice`.
  - Login pasien/kurir yang belum verifikasi → kirim ulang tautan + redirect notice. **Admin dikecualikan** dari gate ini.
  - Gate `verified` belum dipasang di grup route mana pun: user belum verifikasi masih bisa membuka `/home` setelah login. Pasang middleware `verified` bila ingin penegakan penuh.
- **Pitfall yang pernah terjadi**: `MustVerifyEmail` sudah di-`implements` tetapi `use`-nya masih dikomentari → `Interface "App\Models\MustVerifyEmail" not found`, aplikasi fatal total (semua request error). Selalu jalankan `php -l` + `php artisan tinker` setelah menyentuh model.
- Riwayat migrasi: kolom `email_verified_at` sudah ada sejak migrasi awal, tidak perlu migrasi tambahan.

## Build Frontend (Blocker yang Sering Muncul)
- `npm run build` GAGAL di Node.js 18 (`CustomEvent is not defined`) — Vite 8 butuh Node 20.19+/22.12+.
  Node 20 tersedia di `~/.nvm/versions/node/v20.20.2/bin` → `export PATH="$HOME/.nvm/versions/node/v20.20.2/bin:$PATH"` sebelum build.
- Di Node 20 muncul `Cannot find module '@rolldown/binding-linux-x64-gnu'` (bug optional-deps npm). Obat aman tanpa menyentuh lockfile:
  `npm i --no-save @rolldown/binding-linux-x64-gnu@1.0.0-rc.18`
- Layout `layouts/app.blade.php` TIDAK memakai `@vite` (Tailwind via CDN), jadi build hanya untuk pipeline standar — halaman tetap jalan tanpa build.
- `tests/Feature/ExampleTest` memang sudah gagal sejak sebelum perubahan (route `/` redirect ke login, bukan 200). Jangan dianggap regresi.
- `pint app/ routes/` masih menyisakan isu gaya di file lama (ProfileController, AdminMiddleware, Delivery, dll). Jalankan Pint hanya pada file yang diubah; jangan `pint` massal agar diff tidak melebar.
