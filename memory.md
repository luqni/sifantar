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
- **2026-11-27 — PENINGKATAN & PENYELARASAN ALUR LIVE TRACKING (Real-Time Tracking Engine)**:
  - **Otorisasi Multi-Peran**: `HomeController::tracking` dan `HomeController::trackingApi` sebelumnya hanya mengizinkan `patient_id === Auth::id()`, menyebabkan HTTP 404 saat Admin atau Kurir membuka fitur lacak dari riwayat/dashboard. Diperbaiki agar Admin dapat melacak seluruh pesanan, Kurir dapat melacak tugas yang diembannya atau yang berstatus `awaiting_courier`, dan Pasien melacak pesanannya sendiri.
  - **Perbaikan Alur Status Pasien & Admin**:
    - `HomeController::index`: Sebelumnya tidak menyertakan `awaiting_courier` pada query beranda pasien sehingga pesanan aktif sempat lenyap saat admin memanggil kurir. Telah diperbaiki mencakup `['pending', 'preparing', 'ready', 'awaiting_courier', 'delivering']`.
    - `AdminController::dashboard`: Menambahkan `delivering` ke pesanan aktif admin dan menghitung total pengantaran (`awaiting_courier` + `delivering`). Tombol pintas "Lacak" langsung disediakan pada kartu dashboard admin dan halaman detail pengantaran admin.
  - **Penyelarasan Tampilan & Frontend Tracking (`pages/tracking.blade.php`)**:
    - **Peta Leaflet Alami**: Menghapus filter CSS invert negatif agar tampilan OpenStreetMap terang, jelas, dan natural. Marker apotek (hijau), tujuan pasien (oranye), dan kurir bergerak (hijau animasi radar) terintegrasi dengan penyesuaian zoom (`fitBounds`) otomatis.
    - **Stepper Timeline 4 Tahap Sekuensial**: 1. Resep & Obat Disiapkan ➔ 2. Paket Siap di Apotek ➔ 3. Sedang Diantar Kurir ➔ 4. Obat Telah Diterima.
    - **Dynamic Live Polling**: Polling tiap 4 detik via `tracking.api` memperbarui badge status header, estimasi kedatangan (ETA) live, jarak tempuh, kartu profil kurir (nama + tombol panggil telepon), dan pergerakan marker kurir secara dinamis tanpa perlu reload halaman.
  - **Pengujian & Verifikasi**: Test suite `tests/Feature/LiveTrackingFlowTest.php` menguji seluruh alur live tracking multi-peran (37 assertions passed). Total test suite mencapai 4 file (69 assertions passed).

- **2026-11-27 — TAKEOUT INPUT DAFTAR OBAT PASIEN & MEKANISME INPUT OBAT ADMIN/APOTEKER**:
  - **Sisi Pasien (`delivery-request.blade.php`)**: Field input textarea `medicine_list` dihapus sepenuhnya sesuai arahan Yang Mulia Baginda Raja Luqni Baehaqi. Digantikan kartu informasi resmi "Verifikasi Obat oleh Apoteker" yang menjelaskan bahwa resep & obat akan diperiksa dan diinput langsung oleh petugas farmasi. Input alamat pengantaran dipertahankan dengan deteksi GPS real-time.
  - **Controller Pasien (`DeliveryRequestController.php`)**: Validasi `medicine_list` di-takeout; pesanan langsung dibuat dengan status `pending` dan `total_price = 0`; auto-chat ke admin/apoteker diperbarui meminta apoteker memeriksa resep dan menginput obat.
  - **Sisi Admin / Apoteker (`admin-delivery-detail.blade.php` & `AdminController.php`)**:
    - Tersedia kartu kelola obat lengkap: menampilkan daftar obat, qty, harga satuan, subtotal, dan tombol hapus jika status masih `pending`/`preparing`/`ready`.
    - Form input obat baru dilengkapi `<datalist>` dari master `Medicine` dengan fitur auto-fill harga, input qty, dan kalkulasi otomatis `total_price` pesanan.
    - Penambahan route: `POST /admin/delivery/{id}/items` (`admin.delivery.items.store`), `DELETE /admin/delivery/{id}/items/{itemId}` (`admin.delivery.items.destroy`), dan `PUT /admin/delivery/{id}/total-price`.
    - Sinkronisasi alur status: `pending` (Terima & Mulai Siapkan Obat) → `preparing` (Selesai Disiapkan) → `ready` (Panggil Kurir) → `awaiting_courier` (diambil oleh kurir di dashboard).
  - **Toleransi Data Kosong**: View `admin-dashboard`, `delivery-detail`, dan `history` diperbarui agar menampilkan pesan informatif ("Menunggu input resep/obat apoteker") saat item pesanan baru belum diinput.
  - **Pengujian & Verifikasi**: Test suite `tests/Feature/DeliveryRequestFlowTest.php` mencakup alur lengkap: request pasien tanpa obat → auto-chat → admin membuka detail → input obat 1 & 2 → hapus obat 1 → update status workflow. Seluruh 32 assertions passed!

- **2026-11-20 — TOMBOL "BELI LAGI" DIAKTIFKAN (Takeout)**: Sebelumnya `history.blade.php` hanya punya `<button>` mati tanpa aksi.
  - `history.blade.php`: tombol jadi `<a href="{{ route('delivery-request.create', ['from' => $delivery->id]) }}">` **hanya bila `status === 'completed'`**; status lain tetap `<button disabled>` abu pudar. Ikon `rotate-ccw`.
  - `DeliveryRequestController::create(Request $request)` sekarang menerima query `?from=ID`: ambil `Delivery::with('items')` yang **`where('patient_id', Auth::id())`** (WAJIB — kalau tidak, pasien bisa mengintip/menyalin order pasien lain), lalu render view dengan `compact('repeat','medicines')`. `$medicines` = nama obat per baris (`medicine->name` fallback `medicine_name`).
  - `delivery-request.blade.php`: banner hijau "Formulir diisi otomatis dari pesanan #TRACKING" saat `$repeat` ada; textarea pakai `old('medicine_list', $medicines ?? '')` & `old('address', $repeat->delivery_address ?? '')` supaya isian tidak hilang saat validasi gagal.
  - `store()` tidak diubah — alur tetap buat `Delivery` baru + `DeliveryItem` per baris + auto-chat ke admin.
  - Verifikasi curl nyata (login `pasien@sifantar.com` / sandi seeder `password`): `/history` 200 memuat `delivery-request?from=1,2,4,5`; `?from=1` → textarea `Vitamin C` + alamat `Citayam` + banner tracking; `?from=6` (milik pasien lain) & `?from=999` → form kosong tanpa banner; submit → 302 `/home`, DB berisi `ORD-20261120-4AD4` pending + auto-chat admin. Data uji sudah dihapus lagi.
  - ⚠️ Sandi seeder pasien `pasien@sifantar.com` adalah `password` (bukan `password123`).

- **2026-11-20 — REGISTER KURIR + LUPA/RESET PASSWORD + SEEDER ADMIN**:
  - **Register 2 tipe akun**: `pages/register.blade.php` kini punya tab Pasien/Kurir. Halaman kurir khusus `pages/register-kurir.blade.php` (`/{page}` = `register-kurir`) yang hanya `@include('pages.register', ['forceMode' => 'kurir'])` — markup tunggal, tidak duplikat. Mode kurir menampilkan field tambahan: `phone` (wajib), `address` (wajib), `vehicle` (motor/mobil/sepeda), `vehicle_plate` (wajib), `identity_number`/NIK (wajib). Field ini hanya disimpan ke kolom `users` yang sudah ada (phone, address) — data kendaraan/NIK **divalidasi tapi belum dipersist** (belum ada kolom; butuh migrasi bila ingin disimpan).
  - `AuthController::register` menormalisasi `role` (`kurir|courier` → `courier`, sisanya `patient`) lewat `$request->merge()` **sebelum** `validate()`. ⚠️ **PITFALL yang sudah diperbaiki**: nilai hasil `merge()` tidak otomatis masuk `$data` hasil `validate()` — wajib cantumkan `'role' => ['required','in:patient,courier']` di aturan validasi, kalau tidak → `Undefined array key "role"` (HTTP 500).
  - Kurir setelah register langsung `Auth::login` + redirect `courier.dashboard`; pasien → `page:home`. Keduanya `email_verified_at = now()`.
  - Checkbox `terms` wajib (`accepted`) — error message kustom.
  - CSS baru `.btn-primary-orange` di `layouts/app.blade.php` (tombol kurir oranye).
  - **Lupa/reset password** (pakai broker bawaan Laravel, tabel `password_reset_tokens` sudah ada): `GET/POST /forgot-password` (`password.request`, `password.email` + `throttle:6,1`), `GET /reset-password/{token}` (`password.reset`), `POST /reset-password` (`password.update`). View: `pages/forgot-password.blade.php`, `pages/reset-password.blade.php`. Login page dapat tautan "Lupa Kata Sandi?".
  - **Admin bantu reset**: `POST /admin/users/{id}/send-reset-link` (`admin.users.send-reset-link`) → tombol ✉️ di `pages/admin-users.blade.php`. Tombol lama reset-paksa ke `password123` tetap ada. `AdminController::sendResetLink()` mengembalikan flash `error` juga (view sudah menampilkan `session('error')`).
  - **Seeder**: `database/seeders/AdminUserSeeder.php` — idempoten (`updateOrCreate`-style manual), **tidak menimpa password** admin yang sudah ada, kredensial bisa diatur via env `ADMIN_EMAIL`/`ADMIN_PASSWORD`/`ADMIN_NAME`/`ADMIN_PHONE`. Jalankan `php artisan db:seed --class=AdminUserSeeder`. Catatan: admin lama `admin@sifantar.com` password-nya **bukan** `password123` (diubah manual sebelumnya) — seeder sengaja membiarkannya.
  - Verifikasi nyata (curl ke `php artisan serve`): register kurir → 302 `/courier/dashboard` + halaman 200; validasi NIK kosong → balik `/register-kurir` + pesan; forgot → token tersimpan di DB + email tertulis di `storage/logs/laravel.log`; reset dengan token dari log → 302 `/index`, token terhapus, login sandi baru sukses & sandi lama gagal; register pasien tetap normal.
  - **Pitfall uji curl**: `GET /` mengembalikan 302 ke `/index` — token CSRF hanya ada di halaman final, jadi pakai `curl -sL -c/-b` (follow redirect) saat mengambil `_token`, kalau tidak semua POST jadi HTTP 419.

- **2026-11-20 — VERIFIKASI EMAIL DIMATIKAN (takeout)**: Seluruh alur verifikasi email sudah dihapus atas permintaan Luqni.
  - `App\Models\User` tidak lagi `implements MustVerifyEmail` (import ikut dihapus).
  - `app/Http/Controllers/Auth/EmailVerificationController.php` dihapus; `resources/views/pages/verify-email.blade.php` dihapus.
  - Route `verification.notice` / `verification.verify` / `verification.send` dihapus dari `routes/web.php`.
  - Register di `AuthController::register` langsung mengisi `email_verified_at => now()` lalu login + redirect `/home` — user baru bebas akses tanpa verifikasi.
  - `email_verified_at` tetap ada di `#[Fillable]` dan cast `datetime` agar kolom DB tidak memicu mass-assignment error bila dipakai lagi.
  - Status: diverifikasi `php -l` bersih, `route:list` tanpa route verifikasi, grep view/route verifikasi kosong.

- **Riwayat (SUPERSEDED) — Verifikasi Email (MustVerifyEmail)**: `App\Models\User` meng-implements `Illuminate\Contracts\Auth\MustVerifyEmail`. Handler ada di `app/Http/Controllers/Auth/EmailVerificationController.php` (notice/verify/send) dengan route `verification.notice`, `verification.verify` (middleware `signed` + `throttle:6,1`), `verification.send`. View: `pages/verify-email.blade.php`.
  - Register → kirim `sendEmailVerificationNotification()` → redirect `verification.notice`.
  - Login pasien/kurir yang belum verifikasi → kirim ulang tautan + redirect notice. **Admin dikecualikan** dari gate ini.
  - Gate `verified` belum dipasang di grup route mana pun: user belum verifikasi masih bisa membuka `/home` setelah login. Pasang middleware `verified` bila ingin penegakan penuh.
- **Pitfall yang pernah terjadi**: `MustVerifyEmail` sudah di-`implements` tetapi `use`-nya masih dikomentari → `Interface "App\Models\MustVerifyEmail" not found`, aplikasi fatal total (semua request error). Selalu jalankan `php -l` + `php artisan tinker` setelah menyentuh model.
- Riwayat migrasi: kolom `email_verified_at` sudah ada sejak migrasi awal, tidak perlu migrasi tambahan.
- **Pitfall: "email tidak terkirim" saat lokal = NORMAL.** `MAIL_MAILER=log` cuma menulis isi email ke `storage/logs/laravel.log`. Ambil link verifikasi terbaru dengan:
  `grep -o "http://localhost:8000/email/verify/[^)]*" storage/logs/laravel.log | tail -1`
  Untuk benar-benar kirim ke inbox: ganti `.env` ke `MAIL_MAILER=smtp` + kredensial (Gmail butuh App Password 16 digit, bukan password akun).
- **Shortcut verifikasi manual (dev)**: `php artisan tinker --execute="App\Models\User::find(ID)->forceFill(['email_verified_at'=>now()])->save();"`

## Build Frontend (Blocker yang Sering Muncul)
- `npm run build` GAGAL di Node.js 18 (`CustomEvent is not defined`) — Vite 8 butuh Node 20.19+/22.12+.
  Node 20 tersedia di `~/.nvm/versions/node/v20.20.2/bin` → `export PATH="$HOME/.nvm/versions/node/v20.20.2/bin:$PATH"` sebelum build.
- Di Node 20 muncul `Cannot find module '@rolldown/binding-linux-x64-gnu'` (bug optional-deps npm). Obat aman tanpa menyentuh lockfile:
  `npm i --no-save @rolldown/binding-linux-x64-gnu@1.0.0-rc.18`
- Layout `layouts/app.blade.php` TIDAK memakai `@vite` (Tailwind via CDN), jadi build hanya untuk pipeline standar — halaman tetap jalan tanpa build.
- `tests/Feature/ExampleTest` memang sudah gagal sejak sebelum perubahan (route `/` redirect ke login, bukan 200). Jangan dianggap regresi.
- `pint app/ routes/` masih menyisakan isu gaya di file lama (ProfileController, AdminMiddleware, Delivery, dll). Jalankan Pint hanya pada file yang diubah; jangan `pint` massal agar diff tidak melebar.
