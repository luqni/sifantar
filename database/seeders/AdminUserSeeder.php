<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun admin SIFANTAR.
 *
 * Aman dijalankan berulang (updateOrCreate) dan TIDAK menimpa password
 * admin yang sudah ada, supaya admin tidak terkunci setelah ganti sandi.
 *
 * Jalankan: php artisan db:seed --class=AdminUserSeeder
 * Opsi kredensial: ADMIN_EMAIL, ADMIN_PASSWORD, ADMIN_NAME, ADMIN_PHONE pada .env
 *
 * Alur lupa kata sandi:
 *   - User memakai halaman /forgot-password (email reset otomatis).
 *   - Jika email bermasalah, admin login lalu buka /admin/users →
 *     tombol "Kirim Link Reset" untuk mengirim tautan, atau
 *     "Reset ke password123" untuk reset langsung.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@sifantar.com');
        $password = env('ADMIN_PASSWORD', 'password123');
        $name = env('ADMIN_NAME', 'Admin Farmasi');
        $phone = env('ADMIN_PHONE', '081234567890');

        $admin = User::where('email', $email)->first();

        if ($admin) {
            // Perbarui profil & pastikan role tetap admin, password dibiarkan.
            $admin->forceFill([
                'name' => $name,
                'role' => 'admin',
                'phone' => $admin->phone ?: $phone,
                'email_verified_at' => $admin->email_verified_at ?: now(),
            ])->save();

            $this->command?->warn("Admin {$email} sudah ada — profil diperbarui, password TIDAK diubah.");

            return;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'phone' => $phone,
            'email_verified_at' => now(),
        ]);

        $this->command?->info("Admin dibuat: {$email} (password default: {$password}) — segera ganti setelah login pertama.");
    }
}
