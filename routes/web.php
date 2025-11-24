<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\PublicController;
use App\Http\Controllers\ClienteController;

// Rutas Públicas (sin autenticación)
Route::get('/', [PublicController::class, 'home'])->name('public.home');
Route::get('/catalogo', [PublicController::class, 'catalogo'])->name('public.catalogo');
Route::get('/nosotros', [PublicController::class, 'nosotros'])->name('public.nosotros');
Route::get('/hacer-pedido', [PublicController::class,'hacerPedido'])->name('public.hacer-pedido');
Route::post('/hacer-pedido', [PublicController::class, 'guardarPedido'])->name('public.guardar-pedido');
Route::get('/gracias', [PublicController::class, 'gracias'])->name('public.gracias');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Use Case 1: User Management
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Use Case 2: Portfolio Management
    Route::resource('portfolio', \App\Http\Controllers\PortfolioController::class);

    // Use Case 3: Order Management
    Route::resource('orders', \App\Http\Controllers\OrderController::class);

    // Use Case 4: Inventory Management
    Route::resource('inventory', \App\Http\Controllers\InventoryController::class);
    Route::post('inventory/{inventory}/movement', [\App\Http\Controllers\InventoryController::class, 'storeMovement'])->name('inventory.movement.store');

    // Use Case 5: Promotions Management
    Route::resource('promotions', \App\Http\Controllers\PromotionController::class);

    // Use Case 6: Reviews Management
    Route::resource('reviews', \App\Http\Controllers\ReviewController::class)->only(['index', 'store', 'destroy']);

    // Use Case 7: Payments Management
    Route::resource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'store', 'destroy']);
    Route::post('payments/generate-qr', [\App\Http\Controllers\PaymentController::class, 'generateQr'])->name('payments.generate-qr');
    Route::get('payments/{payment}/check-status', [\App\Http\Controllers\PaymentController::class, 'checkTransactionStatus'])->name('payments.check-status');

    // Use Case 8: Reports & Statistics
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
});

// Rutas específicas para CLIENTES (fuera del middleware anterior)
Route::middleware('auth')->group(function () {
    Route::get('/mi-cuenta', [ClienteController::class, 'miCuenta'])->name('cliente.mi-cuenta');
    Route::get('/mis-pedidos', [ClienteController::class, 'misPedidos'])->name('cliente.mis-pedidos');
    Route::get('/mis-pedidos/{id}', [ClienteController::class, 'verPedido'])->name('cliente.ver-pedido');
});

// Callback público para PagoFácil (fuera de auth)
Route::post('payments/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payments.callback');

require __DIR__.'/auth.php';
