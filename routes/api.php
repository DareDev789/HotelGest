<?php

use App\Http\Controllers\AccomptesController;
use App\Http\Controllers\AgencesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DepensesController;
use App\Http\Controllers\FacturesController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\MigrationMenuBarController;
use App\Http\Controllers\PrestationsController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ServicesDivers;
use App\Http\Controllers\SocieteCategorieMenuController;
use App\Http\Controllers\SocieteCommandesController;
use App\Http\Controllers\SocieteFinanceService;
use App\Http\Controllers\SocieteMenuController;
use App\Http\Controllers\SocieteProduitsController;
use App\Http\Controllers\SocieteProfilController;
use App\Http\Controllers\SocieteStockProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BungalowsController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\AllMigrationsController;

Route::middleware('auth:sanctum')->post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});

// Authentification
Route::post('login', [AuthController::class, 'login']);
Route::post('allMigrationsHotel', [AllMigrationsController::class, 'allMigrationsHotel']);
Route::post('allMigrationsUsers', [AllMigrationsController::class, 'allMigrationsUsers']);
Route::post('allMigrationsAgences', [AllMigrationsController::class, 'allMigrationsAgences']);
Route::post('allMigrationsClients', [AllMigrationsController::class, 'allMigrationsClients']);
Route::post('allMigrationsBungalows', [AllMigrationsController::class, 'allMigrationsBungalows']);
Route::post('allMigrationsPrestaions', [AllMigrationsController::class, 'allMigrationsPrestaions']);
Route::post('allMigrationsDivers', [AllMigrationsController::class, 'allMigrationsDivers']);
Route::post('allMigrationsTypeBungalows', [AllMigrationsController::class, 'allMigrationsTypeBungalows']);
Route::post('allMigrationsFactureSetting', [AllMigrationsController::class, 'allMigrationsFactureSetting']);
Route::post('allMigrationsReservations', [AllMigrationsController::class, 'allMigrationsReservations']);
Route::post('allMigrationsDetailsPrestaions', [AllMigrationsController::class, 'allMigrationsDetailsPrestaions']);
Route::post('allMigrationsReservationsDivers', [AllMigrationsController::class, 'allMigrationsReservationsDivers']);
Route::post('allMigrationsAccomptes', [AllMigrationsController::class, 'allMigrationsAccomptes']);


Route::post('allMigrationsCategorieMenu', [MigrationMenuBarController::class, 'allMigrationsCategorieMenu']);
Route::post('allMigrationsMenu', [MigrationMenuBarController::class, 'allMigrationsMenu']);
Route::post('allMigrationsCategorieProduit', [MigrationMenuBarController::class, 'allMigrationsCategorieProduit']);
Route::post('allMigrationsProduits', [MigrationMenuBarController::class, 'allMigrationsProduits']);
Route::post('allMigrationsCommandes', [MigrationMenuBarController::class, 'allMigrationsCommandes']);
Route::post('allMigrationsDetailsProduit', [MigrationMenuBarController::class, 'allMigrationsDetailsProduit']);
Route::post('allMigrationsDetailsMenu', [MigrationMenuBarController::class, 'allMigrationsDetailsMenu']);
Route::post('allMigrationsDepenses', [MigrationMenuBarController::class, 'allMigrationsDepenses']);
Route::post('allMigrationsAccomptesCommandes', [MigrationMenuBarController::class, 'allMigrationsAccomptesCommandes']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('bungalows')->group(function () {
        Route::get('/', [BungalowsController::class, 'getAllBungalows']);
        Route::post('/', [BungalowsController::class, 'createOneBungalow']);
        Route::put('/{id}', [BungalowsController::class, 'updateBungalow']);
        Route::delete('/{id}', [BungalowsController::class, 'deleteBungalow']);
        Route::get('/{id}', [BungalowsController::class, 'getOneBungalow']);
    });


    Route::prefix('agences')->group(function () {
        Route::get('/', [AgencesController::class, 'getAllAgences']);
        Route::post('/', [AgencesController::class, 'CreateAgence']);
        Route::put('/{id}', [AgencesController::class, 'updateAgence']);
        Route::delete('/{id}', [AgencesController::class, 'deleteAgence']);
    });


    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientsController::class, 'getAllClients']);
        Route::post('/', [ClientsController::class, 'CreateClients']);
        Route::put('/{id}', [ClientsController::class, 'updateClients']);
        Route::delete('/{id}', [ClientsController::class, 'deleteClient']);
    });


    Route::prefix('divers')->group(function () {
        Route::get('/', [ServicesDivers::class, 'getAllServicesDivers']);
        Route::post('/', [ServicesDivers::class, 'CreateserviceDivers']);
        Route::put('/{id}', [ServicesDivers::class, 'updateserviceDivers']);
        Route::delete('/{id}', [ServicesDivers::class, 'DeleteserviceDivers']);
    });


    Route::prefix('prestations')->group(function () {
        Route::get('/', [PrestationsController::class, 'getAllprestations']);
        Route::post('/', [PrestationsController::class, 'CreatePrestation']);
        Route::put('/{id}', [PrestationsController::class, 'updatePrestations']);
        Route::delete('/{id}', [PrestationsController::class, 'deletePrestation']);
    });

    Route::prefix('accomptes')->group(function () {
        Route::post('/', [AccomptesController::class, 'createAccompte']);
        Route::put('/{id}', [AccomptesController::class, 'updateAccompte']);
        Route::put('paid/{id}', [AccomptesController::class, 'PaidAccompte']);
    });

    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationsController::class, 'getAllReservations']);
        Route::post('/', [ReservationsController::class, 'createReservation']);
        Route::get('/{id_reservation}', [ReservationsController::class, 'getOneReservation']);
        Route::delete('/{id_reservation}', [ReservationsController::class, 'AnnulerOneReservation']);
        Route::put('/{id_reservation}', [ReservationsController::class, 'ConfirmOneReservation']);
        Route::put('edit/{id_reservation}', [ReservationsController::class, 'UpdateReservation']);
        Route::prefix('detail')->group(function () {
            Route::post('/', [ReservationsController::class, 'AddDetailReservation']);
            Route::delete('/{id}', [ReservationsController::class, 'DeleteDetailReservation']);
            Route::put('/{id}', [ReservationsController::class, 'UpdateDetailReservation']);
        });

        Route::prefix('detailDivers')->group(function () {
            Route::post('/', [ReservationsController::class, 'AddDetailDivers']);
            Route::delete('/{id}', [ReservationsController::class, 'DeleteDetailDivers']);
            Route::put('/{id}', [ReservationsController::class, 'UpdateDetailDivers']);
        });
    });


    Route::prefix('hotel')->group(function () {
        Route::get('/', [HotelController::class, 'getHotelInfo']);
        Route::post('/', [HotelController::class, 'updateHotelInfo']);
    });

    Route::prefix('profil')->group(function () {
        Route::get('/', [SocieteProfilController::class, 'getUserInfo']);
        Route::post('/', [SocieteProfilController::class, 'updateUserInfo']);
        Route::get('AllProfil', [SocieteProfilController::class, 'getAllUsers']);
    });

    Route::prefix('facture')->group(function () {
        Route::get('/', [HotelController::class, 'getHotelSettingFacture']);
        Route::put('/{id}', [HotelController::class, 'updateHotelSettingFactures']);
    });

    Route::prefix('generatefacture')->group(function () {
        Route::get('/', [FacturesController::class, 'getLastFactureNumber']);
        Route::post('/', [FacturesController::class, 'Printfacture']);
        Route::post('/{id}', [FacturesController::class, 'SaveFacture']);
    });

    Route::prefix('menus')->group(function () {
        Route::get('/', [SocieteMenuController::class, 'getAllMenu']);
        Route::post('/', [SocieteMenuController::class, 'CreateMenu']);
        Route::put('/{id}', [SocieteMenuController::class, 'updateMenu']);
        Route::delete('/{id}', [SocieteMenuController::class, 'destroy']);
    });

    Route::prefix('produits')->group(function () {
        Route::get('/', [SocieteProduitsController::class, 'getAllProduits']);
        Route::post('/', [SocieteProduitsController::class, 'CreateProduit']);
        Route::put('/{id}', [SocieteProduitsController::class, 'updateProduit']);
        Route::delete('/{id}', [SocieteProduitsController::class, 'destroy']);
        Route::prefix('changer-categorie')->group(function () {
            Route::post('/', [SocieteProduitsController::class, 'updateCategorieProduit']);
        });
        Route::prefix('stock')->group(function () {
            Route::post('/', [SocieteStockProduit::class, 'AddStock']);
            Route::get('{id}', [SocieteStockProduit::class, 'showStock']);
            Route::delete('{id}', [SocieteStockProduit::class, 'DeleteStock']);
        });
    });


    Route::prefix('categories-menus')->group(function () {
        Route::get('/', [SocieteCategorieMenuController::class, 'getAllCategorieMenu']);
        Route::post('/', [SocieteCategorieMenuController::class, 'CreateCategorieMenu']);
        Route::put('/{id}', [SocieteCategorieMenuController::class, 'updateCategorieMenu']);
        Route::delete('/{id}', [SocieteCategorieMenuController::class, 'destroy']);
    });

    Route::prefix('categories-produits')->group(function () {
        Route::get('/', [ProduitController::class, 'getCategoriesProduitsAvecStock']);
        Route::post('/', [ProduitController::class, 'CreateCategorieProduit']);
        Route::put('{id}', [ProduitController::class, 'updateCategorieProduit']);
        Route::delete('{id}', [ProduitController::class, 'destroy']);
    });

    Route::prefix('commandes')->group(function () {
        Route::get('/', [SocieteCommandesController::class, 'getAllCommandes']);
        Route::get('{id}', [SocieteCommandesController::class, 'getOneCommande']);
        Route::post('/', [SocieteCommandesController::class, 'createCommandes']);
        Route::prefix('details')->group(function () {
            Route::post('produit', [SocieteCommandesController::class, 'CreateDetailProduit']);
            Route::post('menu', [SocieteCommandesController::class, 'CreateDetailMenu']);
            Route::delete('produit/{id}', [SocieteCommandesController::class, 'DeleteDetailProduit']);
            Route::delete('menu/{id}', [SocieteCommandesController::class, 'DeleteDetailMenu']);
        });

        Route::prefix('accomptes')->group(function () {
            Route::post('/', [AccomptesController::class, 'createAccompteCommandes']);
            Route::put('/{id}', [AccomptesController::class, 'updateAccompteCommandes']);
            Route::put('paid/{id}', [AccomptesController::class, 'PaidAccompteCommandes']);
        });
    });

    Route::prefix('depenses')->group(function () {
        Route::get('/', [DepensesController::class, 'getAllDepenses']);
        Route::post('/', [DepensesController::class, 'CreateDepenses']);
        Route::delete('{id}', [DepensesController::class, 'DeleteDepenses']);
    });

    Route::prefix('statistics')->group(function () {
        Route::get('/', [SocieteFinanceService::class, 'getFinancesYear']);
    });
});
