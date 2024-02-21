<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => [ 'jwt.auth'],'prefix'=>'auth'], function () {
});
Route::GET('vue',function(){
    return view('index', [
        'price' => request('price'),
        'gestion_cycle_id' => request('gestion_cycle_id'),
        'participation_Tontine_id' => request('participation_Tontine_id')
    ]);
})->name('payment.index');
Route::post('/checkout', [PaymentController::class, 'payment'])->name('payment.submit');

Route::get('ipn', [PaymentController::class, 'ipn'])->name('paytech-ipn');
Route::get('payment-success/{code}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('payment/{code}/success', [PaymentController::class, 'paymentSuccessView'])->name('payment.success.view');
Route::get('payment-cancel', [PaymentController::class, 'cancel'])->name('paytech.cancel');

