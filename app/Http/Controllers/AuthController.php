<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (Auth::user()->isCourier()) {
                return redirect()->route('courier.dashboard');
            }

            return redirect()->route('page', 'home');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Registrasi dua tipe akun: pasien (default) dan kurir/mitra.
     * Data kendaraan & identitas kurir wajib, selebihnya opsional.
     */
    public function register(Request $request)
    {
        $request->merge([
            'role' => $request->input('role') === 'courier' || $request->input('role') === 'kurir' ? 'courier' : 'patient',
        ]);

        $isCourier = $request->input('role') === 'courier';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:patient,courier'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => [$isCourier ? 'required' : 'nullable', 'string', 'max:20'],
            'address' => [$isCourier ? 'required' : 'nullable', 'string', 'max:1000'],
            'vehicle' => [$isCourier ? 'required' : 'nullable', 'in:motor,mobil,sepeda'],
            'vehicle_plate' => [$isCourier ? 'required' : 'nullable', 'string', 'max:20'],
            'identity_number' => [$isCourier ? 'required' : 'nullable', 'string', 'max:30'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Yang Mulia perlu menyetujui Syarat & Ketentuan terlebih dahulu.',
            'phone.required' => 'Nomor WhatsApp wajib diisi untuk akun kurir.',
            'address.required' => 'Alamat domisili wajib diisi untuk akun kurir.',
            'vehicle_plate.required' => 'Plat nomor kendaraan wajib diisi.',
            'identity_number.required' => 'NIK / nomor KTP wajib diisi.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        if ($user->isCourier()) {
            return redirect()->route('courier.dashboard')
                ->with('success', 'Pendaftaran kurir berhasil! Lengkapi lokasi Anda agar bisa menerima orderan.');
        }

        return redirect()->route('page', 'home');
    }

    /**
     * Form "Lupa Kata Sandi".
     */
    public function showForgotPassword()
    {
        return view('pages.forgot-password');
    }

    /**
     * Kirim tautan reset password ke email user.
     * Selalu balas dengan pesan netral (tidak membocorkan apakah email terdaftar).
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // Lokal memakai MAIL_MAILER=log, tautan tersimpan di storage/logs/laravel.log.
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan reset kata sandi sudah dikirim ke email Anda. Cek juga folder spam.');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'Tunggu sebentar sebelum meminta tautan baru.']);
        }

        return back()->withErrors(['email' => 'Kami tidak menemukan akun dengan email tersebut.']);
    }

    /**
     * Form reset password dari tautan email.
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('pages.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('page', 'index')
                ->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
        }

        return back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
