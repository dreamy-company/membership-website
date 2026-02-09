<?php

use Livewire\Volt\Volt;

// Fortify
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\RoutePath;

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Transactions\ActivityLog;
use App\Livewire\Admin\Members\Index as MemberIndex;
use App\Livewire\Admin\Members\Details as MemberDetail;
use App\Livewire\Admin\Members\Transaction as MemberTransaction;
use App\Livewire\Admin\Bonuses\Index as BonusesIndex;
use App\Livewire\Admin\Province\Index as ProvinceIndex;
use App\Livewire\Admin\Businesses\Index as BusinessIndex;
use App\Livewire\Admin\Domicilies\Index as DomicileIndex;
use App\Livewire\Members\Dashboard\Index as DashboardIndex;
use App\Livewire\Admin\Withdrawals\Index as WithdrawalIndex;
use App\Livewire\Admin\Dashboard\Index as AdminDashboardIndex;
use App\Livewire\Admin\BonusSettings\Index as BonusSettingsIndex;

// Member Livewire
use App\Livewire\Admin\Transactions\Index as TransactionIndex;
use App\Livewire\Members\Member\Index as DashboardMemberIndex;
use App\Livewire\Admin\BusinessesUsers\Index as BusinessesUsersIndex;
use App\Livewire\Members\Withdrawals\Index as DashboardWithdrawalIndex;
use App\Livewire\Members\Transactions\Index as DashboardTransactionIndex;
use App\Livewire\Members\Profile\Index as DashboardProfileIndex;


// Business Livewire
use App\Livewire\Business\Transactions\Index as BusinessTransactionIndex;
use App\Livewire\Business\Transactions\ActivyLog as BusinessActivityLog;
use App\Models\Member;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

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
});

Route::middleware(['auth', 'role:member'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', DashboardIndex::class)->name('index');
    Route::get('/members', DashboardMemberIndex::class)->name('members');
    Route::get('/transactions', DashboardTransactionIndex::class)->name('transactions');
    Route::get('/withdrawals', DashboardWithdrawalIndex::class)->name('withdrawals');
    Route::get('/profile', DashboardProfileIndex::class)->name('profile');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardIndex::class)->name('index');
    Route::get('/provinces', ProvinceIndex::class)->name('provinces');
    Route::get('/businesses', BusinessIndex::class)->name('businesses');
    Route::get('/domicilies', DomicileIndex::class)->name('domicilies');
    Route::get('/members', MemberIndex::class)->name('members');
    Route::get('/members/detail', MemberDetail::class)->name('members.detail');
    Route::get('/members/transaction/{id}', MemberTransaction::class)->name('members.transaction');
    Route::get('/businesses-users', BusinessesUsersIndex::class)->name('businesses.users');
    Route::get('/bonuses', BonusesIndex::class)->name('bonuses');
    Route::get('/transactions', TransactionIndex::class)->name('transactions');
    Route::get('/activity-log', ActivityLog::class)->name('activity-log');
    Route::get('/withdrawals', WithdrawalIndex::class)->name('withdrawals');
    Route::get('/bonus-settings', BonusSettingsIndex::class)->name('bonus-settings');
});
Route::middleware(['auth', 'role:business'])->prefix('business')->name('business.')->group(function () {
    Route::get('/transactions', BusinessTransactionIndex::class)->name('transactions');
    Route::get('/activity-log', BusinessActivityLog::class)->name('activity-log');
});
