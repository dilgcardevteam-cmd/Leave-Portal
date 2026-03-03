<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.index');
        } elseif ($role === 'hr') {
            return redirect()->route('hr.index');
        } elseif ($role === 'lgmed' || $role === 'dc') {
            return redirect()->route('dc.index');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (Auth::user()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.index');
        } elseif (Auth::user()->role === 'hr') {
            return redirect()->route('hr.index');
        } elseif (Auth::user()->role === 'lgmed' || Auth::user()->role === 'dc') {
            return redirect()->route('dc.index');
        }
    }
    $userId = Auth::id();
    $baseline = \App\Models\UserLeaveCredit::firstOrCreate(
        ['user_id' => $userId],
        ['vl_total' => 0, 'sl_total' => 0, 'credits_total' => 100, 'updated_by' => null]
    );
    $holds = \App\Models\UserCreditHold::where('user_id', $userId)->latest()->paginate(5)->withQueryString();
    $leaveCounts = [
        'total' => \App\Models\Leave::where('user_id', $userId)->count(),
        'pending' => \App\Models\Leave::where('user_id', $userId)->where('status', 'pending')->count(),
        'approved' => \App\Models\Leave::where('user_id', $userId)->where('status', 'approved')->count(),
        'rejected' => \App\Models\Leave::where('user_id', $userId)->where('status', 'rejected')->count(),
    ];
    return view('dashboard', compact('baseline', 'holds', 'leaveCounts'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.show');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/resend-otp', [OtpController::class, 'resend'])->name('otp.resend');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{user}/role', [\App\Http\Controllers\AdminController::class, 'updateRole'])->name('admin.users.role');
    Route::get('/admin/smtp', [\App\Http\Controllers\AdminController::class, 'smtp'])->name('admin.smtp');
    Route::post('/admin/smtp/test', [\App\Http\Controllers\AdminController::class, 'smtpTestSend'])->name('admin.smtp.test');
    Route::post('/admin/users/{user}/credits', [\App\Http\Controllers\AdminController::class, 'updateUserCredits'])->name('admin.users.credits');
    Route::get('/admin/leaves', [\App\Http\Controllers\AdminController::class, 'leaves'])->name('admin.leaves');
    Route::get('/admin/leaves/{leave}', [\App\Http\Controllers\AdminController::class, 'showLeave'])->name('admin.leaves.show');
    Route::post('/admin/leaves/{leave}/status', [\App\Http\Controllers\AdminController::class, 'updateLeaveStatus'])->name('admin.leaves.status');
    // Placeholder for user management routes later
    Route::get('/admin/help', function () { return view('admin.help'); })->name('admin.help');
});

Route::middleware(['auth', 'staff'])->group(function () {
    Route::resource('categories', \App\Http\Controllers\LeaveCategoryController::class)->except(['show']);
});

Route::middleware(['auth', 'hr'])->group(function () {
    Route::get('/hr', [\App\Http\Controllers\HrController::class, 'index'])->name('hr.index');
    Route::get('/hr/leaves', [\App\Http\Controllers\HrController::class, 'leaves'])->name('hr.leaves');
    Route::get('/hr/downloads', [\App\Http\Controllers\HrController::class, 'downloads'])->name('hr.downloads');
    Route::get('/hr/api/calendar', [\App\Http\Controllers\HrController::class, 'calendarData'])->name('hr.calendar.data');
    Route::get('/hr/calendar/export', [\App\Http\Controllers\HrController::class, 'exportCalendar'])->name('hr.calendar.export');
    Route::get('/hr/settings', [\App\Http\Controllers\HrController::class, 'settings'])->name('hr.settings');
    Route::post('/hr/settings/credits/{user}', [\App\Http\Controllers\HrController::class, 'updateCreditManagement'])->name('hr.settings.credits.update');
    Route::post('/hr/settings/credits/{user}/monthly', [\App\Http\Controllers\HrController::class, 'applyMonthlyCredits'])->name('hr.settings.credits.monthly');
    Route::post('/hr/settings/signatories/{user}', [\App\Http\Controllers\HrController::class, 'updateSignatory'])->name('hr.settings.signatory.update');
    Route::get('/hr/leaves/{leave}', [\App\Http\Controllers\HrController::class, 'showLeave'])->name('hr.leaves.show');
    Route::post('/hr/leaves/{leave}/status', [\App\Http\Controllers\HrController::class, 'updateLeaveStatus'])->name('hr.leaves.status');
    Route::post('/hr/leaves/{leave}/credits', [\App\Http\Controllers\HrController::class, 'updateLeaveCredits'])->name('hr.leaves.credits');
    Route::get('/hr/help', [\App\Http\Controllers\HrController::class, 'help'])->name('hr.help');
});
Route::middleware(['auth', 'rd'])->group(function () {
    Route::get('/rd', [\App\Http\Controllers\RdController::class, 'index'])->name('rd.index');
    Route::get('/rd/leaves', [\App\Http\Controllers\RdController::class, 'leaves'])->name('rd.leaves');
    Route::get('/rd/downloads', [\App\Http\Controllers\RdController::class, 'downloads'])->name('rd.downloads');
    Route::get('/rd/leaves/{leave}', [\App\Http\Controllers\RdController::class, 'showLeave'])->name('rd.leaves.show');
    Route::post('/rd/leaves/{leave}/status', [\App\Http\Controllers\RdController::class, 'updateLeaveStatus'])->name('rd.leaves.status');
    Route::post('/rd/leaves/{leave}/recommendation', [\App\Http\Controllers\RdController::class, 'saveRecommendation'])->name('rd.leaves.recommendation');
    Route::get('/rd/help', [\App\Http\Controllers\RdController::class, 'help'])->name('rd.help');
});
Route::middleware(['auth', 'ard'])->group(function () {
    Route::get('/ard', [\App\Http\Controllers\ArdController::class, 'index'])->name('ard.index');
    Route::get('/ard/leaves', [\App\Http\Controllers\ArdController::class, 'leaves'])->name('ard.leaves');
    Route::get('/ard/downloads', [\App\Http\Controllers\ArdController::class, 'downloads'])->name('ard.downloads');
    Route::get('/ard/leaves/{leave}', [\App\Http\Controllers\ArdController::class, 'showLeave'])->name('ard.leaves.show');
    Route::post('/ard/leaves/{leave}/status', [\App\Http\Controllers\ArdController::class, 'updateLeaveStatus'])->name('ard.leaves.status');
    Route::post('/ard/leaves/{leave}/recommendation', [\App\Http\Controllers\ArdController::class, 'saveRecommendation'])->name('ard.leaves.recommendation');
    Route::get('/ard/help', function () { return view('ard.help'); })->name('ard.help');
});
Route::middleware(['auth', 'dc'])->group(function () {
    Route::get('/dc', [\App\Http\Controllers\DcController::class, 'index'])->name('dc.index');
    Route::get('/dc/leaves', [\App\Http\Controllers\DcController::class, 'leaves'])->name('dc.leaves');
    Route::get('/dc/downloads', [\App\Http\Controllers\DcController::class, 'downloads'])->name('dc.downloads');
    Route::get('/dc/leaves/{leave}', [\App\Http\Controllers\DcController::class, 'showLeave'])->name('dc.leaves.show');
    Route::post('/dc/leaves/{leave}/recommendation', [\App\Http\Controllers\DcController::class, 'saveRecommendation'])->name('dc.leaves.recommendation');
    Route::post('/dc/leaves/{leave}/status', [\App\Http\Controllers\DcController::class, 'updateLeaveStatus'])->name('dc.leaves.status');
    Route::get('/dc/help', function () { return view('dc.help'); })->name('dc.help');
});
Route::middleware(['auth'])->group(function () {
    Route::resource('leaves', \App\Http\Controllers\LeaveController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/list', [\App\Http\Controllers\NotificationController::class, 'list'])->name('notifications.list');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    // Credits
    Route::get('/my-credits', [\App\Http\Controllers\CreditsController::class, 'index'])->name('user.credits');
    // PDFs
    Route::get('/leaves/{leave}/pdf', [\App\Http\Controllers\LeavePdfController::class, 'download'])->name('leaves.pdf');
    Route::get('/leaves/{leave}/pdf/view', [\App\Http\Controllers\LeavePdfController::class, 'view'])->name('leaves.pdf.view');
    // User help
    Route::get('/help', function(){ return view('user.help'); })->name('user.help');
});

require __DIR__.'/auth.php';
require __DIR__.'/auth.php';
require __DIR__.'/auth.php';
