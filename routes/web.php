<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MedicationQrController;
use App\Http\Controllers\DeviceInstanceController;
use App\Http\Controllers\EquipmentInventoryController;
use App\Http\Controllers\InspectionLogController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaterialCategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\NfrProtocolController;
use App\Http\Controllers\QrProtocolController;
use App\Http\Controllers\VehicleEquipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// QR-Code Außenprüfung (kein Login erforderlich)
Route::get('/qr/{equipment}',         [QrProtocolController::class, 'showPassword'])->name('qr.password');
Route::post('/qr/{equipment}',        [QrProtocolController::class, 'authenticate'])->name('qr.auth');
Route::get('/qr/{equipment}/neu',     [QrProtocolController::class, 'create'])->name('qr.create');
Route::post('/qr/{equipment}/neu',    [QrProtocolController::class, 'store'])->name('qr.store');
Route::get('/qr/{equipment}/danke',   [QrProtocolController::class, 'success'])->name('qr.success');

// QR-Code Medikamenten-Entnahme (kein Login erforderlich)
Route::get('/med-qr/{medication}',           [MedicationQrController::class, 'showPassword'])->name('med-qr.password');
Route::post('/med-qr/{medication}',          [MedicationQrController::class, 'authenticate'])->name('med-qr.auth');
Route::get('/med-qr/{medication}/entnahme',  [MedicationQrController::class, 'create'])->name('med-qr.create');
Route::post('/med-qr/{medication}/entnahme', [MedicationQrController::class, 'store'])->name('med-qr.store');
Route::get('/med-qr/{medication}/danke',     [MedicationQrController::class, 'success'])->name('med-qr.success');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('materials', MaterialController::class);
    Route::resource('medications', MedicationController::class)->except(['index']);
    Route::get('/medikamente', [MedicationController::class, 'index'])->name('medications.index');
    Route::post('medications/{medication}/batches',              [MedicationController::class, 'storeBatch'])->name('medications.batches.store');
    Route::patch('medications/{medication}/batches/{batch}',     [MedicationController::class, 'updateBatch'])->name('medications.batches.update');
    Route::delete('medications/{medication}/batches/{batch}',    [MedicationController::class, 'destroyBatch'])->name('medications.batches.destroy');
    Route::post('medications/{medication}/checkout',             [MedicationController::class, 'storeCheckout'])->name('medications.checkout.store');
    Route::post('materials/{material}/devices',              [DeviceInstanceController::class, 'store'])->name('materials.devices.store');
    Route::patch('materials/{material}/devices/{instance}', [DeviceInstanceController::class, 'update'])->name('materials.devices.update');
    Route::delete('materials/{material}/devices/{instance}',[DeviceInstanceController::class, 'destroy'])->name('materials.devices.destroy');
    Route::post('vehicles/{vehicle}/equipment/{equipment}/devices/{instance}/assign',  [DeviceInstanceController::class, 'assign'])->name('equipment.devices.assign');
    Route::delete('vehicles/{vehicle}/equipment/{equipment}/devices/{instance}/unassign', [DeviceInstanceController::class, 'unassign'])->name('equipment.devices.unassign');
    Route::resource('categories', MaterialCategoryController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('checkouts', CheckoutController::class);

    Route::resource('vehicles', VehicleController::class);
    Route::resource('vehicles.equipment', VehicleEquipmentController::class)->except(['index']);
    Route::resource('vehicles.equipment.inventory', EquipmentInventoryController::class)->except(['index', 'show']);
    Route::resource('vehicles.equipment.protocols', NfrProtocolController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('vehicles/{vehicle}/equipment/{equipment}/protocols/{protocol}/print', [NfrProtocolController::class, 'printView'])->name('vehicles.equipment.protocols.print');
    Route::patch('vehicles/{vehicle}/equipment/{equipment}/protocols/{protocol}/items/{item}/resolve', [NfrProtocolController::class, 'resolveItem'])->name('vehicles.equipment.protocols.items.resolve');
    Route::get('/nfr-protokolle', [NfrProtocolController::class, 'allIndex'])->name('nfr-protocols.all');
    Route::patch('checkouts/{checkout}/return', [CheckoutController::class, 'markReturned'])->name('checkouts.return');
    Route::resource('inspections', InspectionLogController::class);

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
        Route::get('settings',  [SettingsController::class, 'index'])->name('settings');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('activity',  [ActivityController::class, 'index'])->name('activity');
    });
});

require __DIR__.'/auth.php';
