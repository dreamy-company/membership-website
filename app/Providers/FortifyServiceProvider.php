<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

// Fortify
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

use App\Http\Responses\LoginResponse as CustomLoginResponse;
use App\Http\Responses\LogoutResponse as CustomLogoutResponse;

// Models
use App\Models\User;
use App\Models\Member;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Login Reponse
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
        // Logout Response
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        Fortify::authenticateUsing(function (Request $request) {
            $loginId = $request->login_id;
            $user = null;

            // Cek apakah input adalah format Email yang valid
            $isEmail = filter_var($loginId, FILTER_VALIDATE_EMAIL);

            if ($isEmail) {
                // JALUR 1: Cari berdasarkan Email di tabel users
                $user = User::where('email', $loginId)->first();
            } else {
                // JALUR 2: Cari berdasarkan Member Code di tabel members
                // Menggunakan with('user') untuk mengambil data user relasinya
                $member = Member::where('member_code', $loginId)->with('user')->first();

                if ($member) {
                    $user = $member->user;
                }
            }

            // Verifikasi Password (jika user ditemukan dari salah satu jalur di atas)
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            // Gagal login
            return null;
        });
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn() => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn() => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
