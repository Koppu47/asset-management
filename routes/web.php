<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventorySurveyController;
use App\Http\Controllers\BorrowRecordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes (ทุกคนเข้าถึงได้โดยไม่ต้องล็อกอิน)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    session()->keep(['success', 'error']);

    return redirect()->route('assets.index');
});

Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
Route::get('/assets/{asset}/qr', [AssetController::class, 'printQr'])->name('assets.qr');

/*
|--------------------------------------------------------------------------
| Protected Routes: ผู้ใช้ทั่วไป (ต้อง Login ก่อน)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // ฟังก์ชันการยืมครุภัณฑ์ของพนักงาน
    Route::get('/assets/{asset}/borrow', [BorrowRecordController::class, 'create'])->name('borrows.create');
    Route::post('/assets/{asset}/borrow', [BorrowRecordController::class, 'store'])->name('borrows.store');
    Route::get('/my-borrows', [BorrowRecordController::class, 'myBorrows'])->name('borrows.my');
    Route::delete('/my-borrows/{borrowRecord}', [BorrowRecordController::class, 'cancel'])->name('borrows.cancel');

    // Dashboard & Profile
    Route::get('/dashboard', function () {
        session()->keep(['success', 'error']);

        if (request()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('assets.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Protected Routes: สิทธิ์ผู้ดูแลระบบ (Admin Only - ผ่าน Middleware 'admin')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/inventory-surveys', [InventorySurveyController::class, 'index'])->name('admin.inventory-surveys.index');
    Route::post('/admin/inventory-surveys', [InventorySurveyController::class, 'store'])->name('admin.inventory-surveys.store');
    Route::get('/admin/inventory-surveys/{inventorySurvey}', [InventorySurveyController::class, 'show'])->name('admin.inventory-surveys.show');
    Route::patch('/admin/inventory-surveys/{inventorySurvey}/items/{item}', [InventorySurveyController::class, 'updateItem'])->name('admin.inventory-surveys.items.update');
    Route::patch('/admin/inventory-surveys/{inventorySurvey}/complete', [InventorySurveyController::class, 'complete'])->name('admin.inventory-surveys.complete');
    Route::patch('/admin/inventory-surveys/{inventorySurvey}/items/{item}/follow-up', [InventorySurveyController::class, 'updateFollowUp'])->name('admin.inventory-surveys.items.follow-up');
    Route::get('/admin/reports/assets.csv', [ReportController::class, 'assetsCsv'])->name('admin.reports.assets.csv');
    Route::get('/admin/reports/assets', [ReportController::class, 'assetsPrint'])->name('admin.reports.assets');
    Route::get('/admin/reports/surveys/{inventorySurvey}', [ReportController::class, 'surveyPrint'])->name('admin.reports.surveys');

    // 1. จัดการหมวดหมู่ (Category CRUD)
    Route::resource('categories', CategoryController::class);

    // 2. จัดการครุภัณฑ์ (Asset CRUD - ส่วนสร้าง/แก้ไข/ลบ)
    Route::get('/admin/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/admin/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/admin/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/admin/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/admin/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    // 3. จัดการรายการคำขอยืม-คืนครุภัณฑ์ (Borrow Management)
    Route::get('/admin/borrows', [BorrowRecordController::class, 'index'])->name('admin.borrows.index');
    Route::patch('/admin/borrows/{borrowRecord}/approve', [BorrowRecordController::class, 'approve'])->name('admin.borrows.approve');
    Route::patch('/admin/borrows/{borrowRecord}/reject', [BorrowRecordController::class, 'reject'])->name('admin.borrows.reject');
    Route::patch('/admin/borrows/{borrowRecord}/return', [BorrowRecordController::class, 'returnAsset'])->name('admin.borrows.return');

    // 4. จัดการผู้ใช้งาน (User Management)
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
});

require __DIR__ . '/auth.php';
