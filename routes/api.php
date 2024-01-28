<?php

use App\Models\GestionCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\TontineController;
use App\Http\Controllers\api\PayementController;
use App\Http\Controllers\Api\GestionCycleController;
use App\Http\Controllers\Api\CotisationTontineController;
use App\Http\Controllers\Api\ParticipationTontineController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




Route::POST('registerUser',[UserController::class,'register']);
Route::POST('loginUser',[UserController::class,'login']);
Route::POST('modifierUser',[UserController::class,'update']);
Route::POST('logoutUser',[UserController::class,'logoutUser']);

Route::GET('ListeUser',[UserController::class,'touslesUtilisateurs']);

Route::GET('indexUser',[UserController::class,'index']);


Route::POST('registerAdmin',[AdminController::class,'registerAdmin']);
Route::POST('loginAdmin',[AdminController::class,'loginAdmin']);
Route::POST('logoutAdmin',[AdminController::class,'logoutAdmin']);
Route::GET('indexAdmin',[AdminController::class,'index']);


Route::GET('ListeTontine',[TontineController::class,'tousLesTontines']);

Route::group(['middleware' => [ 'jwt.auth'],'prefix'=>'auth'], function () {

Route::POST('ajouterTontine',[TontineController::class,'demandeCreationTontine']);
Route::POST('ParticiperTontine',[ParticipationTontineController::class,'demandeParticipationTontine']);
Route::POST('faireCotisationTontine/{participationTontines}',[CotisationTontineController::class,'effectuerCotisation']);

});


Route::group(['middleware' => [ 'jwt.auth','acces:participant_tontine'],'prefix'=>'participant_tontine'], function () {

    });



Route::group(['middleware' => [ 'jwt.auth','acces:createur_tontine'],'prefix'=>'createur_tontine'], function () {


    });
    Route::GET('ListeparticipationPartontine/{tontines}',[TontineController::class,'allParticipationParTontine']);
    Route::GET('ListeparticipationAlltontine',[ParticipationTontineController::class,'allParticipation']);




Route::GET('ListeTontineparCreateur/{user}',[TontineController::class,'alltontineparcreateur']);

Route::GET('Tontineparticipe/{user}',[UserController::class,'tontineparticipeParUser']);




Route::group(['middleware' => [ 'jwt.auth','acces:admin'],'prefix'=>'admin'], function () {
    Route::delete('supprimerUser',[UserController::class,'destroy']);

    Route::POST('AcceptedTontine/{tontines}',[TontineController::class,'CreationTontineAccepted']);
    Route::POST('RefuseTontine/{tontines}',[TontineController::class,'CreationTontineRefuse']);
    
    Route::delete('supprimerTontine/{tontines}',[TontineController::class,'destroy']);
    Route::POST('modifierTontine/{tontines}',[TontineController::class,'update']);

    Route::GET('ListeTontineAccepte',[TontineController::class,'tontineAccepte']);

    Route::GET('ListeTontineEnAttente',[TontineController::class,'tontineEnAttente']);
});

Route::POST('AcceptedParticipationUser/{participeTontine}',[ParticipationTontineController::class,'accepteParticipation']);

Route::POST('RefuseParticipationUser/{participeTontineRefuse}',[ParticipationTontineController::class,'refuseParticipation']);
  


Route::GET('listeCotisationTontine',[CotisationTontineController::class,'listeCotisation']);


Route::GET('allCotisaTontine/{cotisations}',[CotisationTontineController::class,'cotisationParparticipation']);


Route::POST('gererCycle/{tontine}',[GestionCycleController::class,'gestionCycle']);



//allCotisationParTontine

Route::GET('ListeCotisationParTontine/{tontines}',[TontineController::class,'allCotisationParTontine']);





//public function notificationCotisation(GestionCycle $cycles,Tontine $tontines)
//mail pour date de notification
Route::POST('notifierDateNotification',[GestionCycleController::class,'notificationCotisation']);





Route::get('/payment', [PayementController::class, 'index'])->name('payment.index');
Route::post('/checkout', [PayementController::class, 'payment'])->name('payment.submit');
Route::get('ipn', [PayementController::class, 'ipn'])->name('paytech-ipn');
Route::get('payment-success/{code}', [PayementController::class, 'success'])->name('payment.success');
Route::get('payment/{code}/success', [PayementController::class, 'paymentSuccessView'])->name('payment.success.view');
Route::get('payment-cancel', [PayementController::class, 'cancel'])->name('paytech.cancel');
