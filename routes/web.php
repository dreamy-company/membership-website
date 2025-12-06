<?php

use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Province\Index as ProvinceIndex;
use App\Livewire\Admin\Businesses\Index as BusinessIndex;
use App\Livewire\Admin\Domicilies\Index as DomicileIndex;
use App\Livewire\Admin\Members\Index as MemberIndex;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/provinces', ProvinceIndex::class)->name('provinces');
            Route::get('/businesses', BusinessIndex::class)->name('businesses');
            Route::get('/domicilies', DomicileIndex::class)->name('domicilies');
            Route::get('/members', MemberIndex::class)->name('members');
        });
    });
