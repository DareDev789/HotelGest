<?php

namespace App\Http\Controllers;

use App\Models\Accomptes;
use App\Models\Categorie_Menu;
use App\Models\Categorie_Produit;
use App\Models\CommandesMenus;
use App\Models\CommandesProduits;
use App\Models\Depenses;
use App\Models\Factures;
use App\Models\Reservations;
use App\Models\SocieteAccomptesCommandes;
use App\Models\SocieteAgence;
use App\Models\SocieteCategorieMenu;
use App\Models\SocieteCategorieProduit;
use App\Models\SocieteClient;
use App\Models\SocieteCommande;
use App\Models\SocieteDepenses;
use App\Models\SocieteDetailsCommandesMenus;
use App\Models\SocieteDetailsCommandesProduits;
use App\Models\SocieteDevise;
use App\Models\SocieteFacturesCommandes;
use App\Models\SocieteHotel;
use App\Models\SocieteMenu;
use App\Models\Menu;
use App\Models\Produit;
use App\Models\SocieteProduit;
use App\Models\SocieteProduitStock;
use App\Models\SocieteUser;
use Carbon\Carbon;
use Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MigrationMenuBarController extends Controller
{
    public function allMigrationsCategorieMenu(Request $request)
    {
        try {
            $categorieMenus = Categorie_Menu::All();

            foreach ($categorieMenus as $categorieMenu) {
                $existingCategorie = SocieteCategorieMenu::where('id', $categorieMenu->id_categorie_menu)->first();

                if (!$existingCategorie) {

                    if ($categorieMenu->id_hotel && !SocieteHotel::where('id_hotel', $categorieMenu->id_hotel)->first()) {
                        Log::warning('hotel introuvable', ['hotel' => $categorieMenu->id_hotel]);
                        continue;
                    }

                    $SocieteCatMenu = new SocieteCategorieMenu();

                    $SocieteCatMenu->id = $categorieMenu->id_categorie_menu;
                    $SocieteCatMenu->nom_categorie_menu = $categorieMenu->nom_categorie_menu;
                    $SocieteCatMenu->id_hotel = $categorieMenu->id_hotel;

                    $SocieteCatMenu->save();
                }
            }

            return response()->json([
                'message' => 'Migration des categories Menus effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des categories Menus', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des categories Menus.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function allMigrationsMenu(Request $request)
    {
        try {
            $Menus = Menu::where('etat', '1')->get();

            foreach ($Menus as $Menu) {
                $existingMenu = SocieteMenu::where('id', $Menu->id_menu)->first();

                if (!$existingMenu) {

                    if ($Menu->id_hotel && !SocieteHotel::where('id_hotel', $Menu->id_hotel)->first()) {
                        Log::warning('hotel introuvable', ['hotel' => $Menu->id_hotel]);
                        continue;
                    }

                    if ($Menu->id_categorie_menu == 0 || ($Menu->id_categorie_menu && !SocieteCategorieMenu::where('id', $Menu->id_categorie_menu)->first())) {
                        Log::warning('hotel introuvable', ['id_categorie_menu' => $Menu->id_categorie_menu]);
                        continue;
                    }

                    $SocieteMenu = new SocieteMenu();

                    $SocieteMenu->id = $Menu->id_menu;
                    $SocieteMenu->id_categorie = $Menu->id_categorie_menu;
                    $SocieteMenu->nom_menu = $Menu->nom_menu;
                    $SocieteMenu->prix_menu = $Menu->prix_menu;
                    $SocieteMenu->autres_info_menu = $Menu->autres_info_menu;
                    $SocieteMenu->id_hotel = $Menu->id_hotel;

                    $SocieteMenu->save();
                }
            }

            return response()->json([
                'message' => 'Migration des Menus effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des Menus', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des Menus.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsCategorieProduit(Request $request)
    {
        try {
            $categorieProduits = Categorie_Produit::All();

            foreach ($categorieProduits as $categorieProduit) {
                $existingCategorie = SocieteCategorieProduit::where('id', $categorieProduit->ID)->first();

                if (!$existingCategorie) {

                    if ($categorieProduit->id_hotel && !SocieteHotel::where('id_hotel', $categorieProduit->id_hotel)->first()) {
                        Log::warning('hotel introuvable', ['hotel' => $categorieProduit->id_hotel]);
                        continue;
                    }

                    $SocieteCatProduit = new SocieteCategorieProduit();

                    $SocieteCatProduit->id = $categorieProduit->ID;
                    $SocieteCatProduit->nom_categorie_produit = $categorieProduit->nom_categorie;
                    $SocieteCatProduit->id_hotel = $categorieProduit->id_hotel;

                    $SocieteCatProduit->save();
                }
            }

            return response()->json([
                'message' => 'Migration des categories Produit effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des categories Produit', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des categories Produit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function allMigrationsProduits(Request $request)
    {
        try {
            DB::beginTransaction();

            Log::info("Début de la migration des produits.");

            // Récupérer tous les hôtels existants pour éviter plusieurs requêtes
            $hotelsExistants = SocieteHotel::pluck('id_hotel')->toArray();

            // Étape 1 : Grouper les produits et insérer dans societe_produit
            $groupedProduits = Produit::selectRaw('MIN(ID) as id, nom_produit, prix_vente, categorie_id, MAX(quantifie) as quantifie, MAX(id_hotel) as id_hotel')
                ->groupBy('nom_produit', 'prix_vente', 'categorie_id')
                ->get();


            $produitMap = []; // Associer les produits aux nouveaux IDs

            foreach ($groupedProduits as $produit) {
                if (!$produit->id_hotel || !in_array($produit->id_hotel, $hotelsExistants)) {
                    Log::warning('Hôtel introuvable, produit ignoré', ['id_hotel' => $produit->id_hotel, 'nom_produit' => $produit->nom_produit]);
                    continue;
                }

                $newProduit = SocieteProduit::create([
                    'id_categorie' => $produit->categorie_id,
                    'nom_produit' => $produit->nom_produit,
                    'prix_vente' => $produit->prix_vente,
                    'quantifie' => (bool) $produit->quantifie,
                    'id_hotel' => $produit->id_hotel,
                ]);

                // Utilisation d'une clé unique (nom_produit + categorie_id) pour éviter les conflits
                $produitMap[$produit->nom_produit . '_' . $produit->categorie_id] = $newProduit->id;
            }

            Log::info("Insertion des produits terminée.");

            // Étape 2 : Migration des stocks
            $stocks = Produit::where('quantifie', true)->get();

            foreach ($stocks as $stock) {
                $produitKey = $stock->nom_produit . '_' . $stock->categorie_id;
                if (!isset($produitMap[$produitKey])) {
                    continue;
                }

                if (!$stock->id_hotel || !in_array($stock->id_hotel, $hotelsExistants)) {
                    Log::warning('Hôtel introuvable, stock ignoré', ['id_hotel' => $stock->id_hotel, 'nom_produit' => $stock->nom_produit]);
                    continue;
                }

                // Vérifier si l'utilisateur existe
                $recupererIdUser = SocieteUser::where('username', $stock->ajout_by)
                    ->where('id_hotel', $stock->id_hotel)
                    ->first();

                $nouveau_prix = $stock->quantite_stock * $stock->prix_achat;

                if ($nouveau_prix > 99999999.99) {
                    continue;
                }

                SocieteProduitStock::insert([
                    'id_produit' => $produitMap[$produitKey],
                    'stock' => $stock->quantite_stock,
                    'prix_achat' => $nouveau_prix,
                    'id_user' => $recupererIdUser ? $recupererIdUser->id : null,
                    'id_hotel' => $stock->id_hotel,
                    'created_at' => $stock->date_ajout,
                ]);
            }

            DB::commit();

            Log::info("Migration des produits et stocks terminée avec succès.");

            return response()->json(['message' => 'Migration effectuée avec succès.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur lors de la migration des produits : " . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la migration des produits.', 'details' => $e->getMessage()], 500);
        }
    }

    public function updatePrixAchat()
    {
        try {
            $stocks = SocieteProduitStock::all();

            foreach ($stocks as $stock) {
                $nouveau_prix = $stock->stock * $stock->prix_achat;
            
                if ($nouveau_prix > 99999999.99) {
                    continue;
                }
            
                $stock->prix_achat = $nouveau_prix;
                $stock->save();
            }
            

            return response()->json([
                'success' => true,
                'message' => 'Prix d\'achat mis à jour avec succès.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsCommandes(Request $request)
    {
        try {
            $commandes = Reservations::select(
                'ident_reservation',
                DB::raw('MAX(id_reservation) as id_reservation'),
                DB::raw('MAX(id_client) as id_client'),
                DB::raw('MAX(id_agence) as id_agence'),
                DB::raw('MAX(id_hotel) as id_hotel'),
                DB::raw('MAX(type_client) as type_client'),
                DB::raw('MAX(devise) as devise'),
                DB::raw('MAX(facture) as facture'),
                DB::raw('MAX(date_reservation) as date_reservation'),
                DB::raw('MAX(etat_reservation) as etat_reservation')
            )
                ->where('etat_reservation', '!=', 'Annule')
                ->where('type_reser', 2)
                ->groupBy('ident_reservation')
                ->get();

            foreach ($commandes as $commande) {

                $devise = SocieteDevise::where('id_hotel', $commande->id_hotel)
                    ->where('type', 'autres')->first();

                $existingCommande = SocieteCommande::where('id_commande', $commande->ident_reservation)->first();

                if (!$existingCommande) {
                    $id_agence = null;

                    if (!$devise) {
                        Log::warning('Devise introuvable', ['id_hotel' => $commande->id_hotel]);
                        continue;
                    }

                    $id_client = empty($commande->id_client) ? null : $commande->id_client;

                    SocieteCommande::insert([
                        'id_commande' => $commande->ident_reservation,
                        'id_client' => $id_client,
                        'id_agence' => $id_agence,
                        'id_hotel' => $commande->id_hotel,
                        'type_client' => $commande->type_client,
                        'statut_reservation' => $commande->facture,
                        'devise' => $devise->devise,
                        'created_at' => $commande->date_reservation,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'commandes' => $commandes,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des commandes', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des commandes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function allMigrationsDetailsMenu(Request $request)
    {
        try {
            $detailsMenus = CommandesMenus::all();

            foreach ($detailsMenus as $detailsMenu) {
                // Vérification des clés étrangères
                if ($detailsMenu->ident_reservation && !SocieteCommande::where('id_commande', $detailsMenu->ident_reservation)->first()) {
                    Log::warning('Commande introuvable ou id_reservation vide', ['id_commande' => $detailsMenu->ident_reservation]);
                    continue;
                }

                if ($detailsMenu->ID && SocieteDetailsCommandesMenus::where('id', $detailsMenu->ID)->first()){
                    continue;
                }
                
                $nomMenu = SocieteMenu::where('nom_menu', $detailsMenu->nom_menu)->where('id_hotel', $detailsMenu->id_hotel)->first();

                if ($nomMenu) {
                    $idMenu = $nomMenu->id;
                } else {
                    $idMenu = null;
                }
                $SocieteDetailsCommandesMenus = new SocieteDetailsCommandesMenus();
                $SocieteDetailsCommandesMenus->id = $detailsMenu->ID;
                $SocieteDetailsCommandesMenus->id_commande = $detailsMenu->ident_reservation;
                $SocieteDetailsCommandesMenus->quantite = $detailsMenu->quantite;
                $SocieteDetailsCommandesMenus->id_menu = $idMenu;
                $SocieteDetailsCommandesMenus->prix_menu = $detailsMenu->prix_menu;
                $SocieteDetailsCommandesMenus->nom_menu = $detailsMenu->nom_menu;
                $SocieteDetailsCommandesMenus->id_hotel = $detailsMenu->id_hotel;

                $SocieteDetailsCommandesMenus->save();

            }

            return response()->json([
                'message' => 'Migration des details de menu effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des details de menu', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des details de menu.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsDetailsProduit(Request $request)
    {
        try {
            $detailsProduits = CommandesProduits::all();

            foreach ($detailsProduits as $detailsProduit) {
                // Vérification des clés étrangères
                if ($detailsProduit->ident_reservation && !SocieteCommande::where('id_commande', $detailsProduit->ident_reservation)->exists()) {
                    Log::warning('Commande introuvable ou id_reservation vide', [
                        'id_commande' => $detailsProduit->ident_reservation
                    ]);
                    continue;
                }

                // Vérification si les données existent avant la requête
                if (empty($detailsProduit->nom_produit) || empty($detailsProduit->id_hotel)) {
                    Log::warning('Données produit incomplètes', [
                        'nom_produit' => $detailsProduit->nom_produit,
                        'id_hotel' => $detailsProduit->id_hotel
                    ]);
                    continue;
                }

                // Recherche du produit dans la table SocieteProduit
                $nomProduit = SocieteProduit::where('nom_produit', $detailsProduit->nom_produit)
                    ->where('id_hotel', $detailsProduit->id_hotel)
                    ->first();

                if (!$nomProduit) {
                    $idproduit = null;
                } else {
                    $idproduit = $nomProduit->id;
                }

                // Création et sauvegarde des détails de commande
                $SocieteDetailsCommandesProduits = new SocieteDetailsCommandesProduits();
                $SocieteDetailsCommandesProduits->id_commande = $detailsProduit->ident_reservation;
                $SocieteDetailsCommandesProduits->quantite = $detailsProduit->quantite;
                $SocieteDetailsCommandesProduits->id_produit = $idproduit;
                $SocieteDetailsCommandesProduits->prix_produit = $detailsProduit->prix_unitaire;
                $SocieteDetailsCommandesProduits->nom_produit = $detailsProduit->nom_produit;
                $SocieteDetailsCommandesProduits->id_hotel = $detailsProduit->id_hotel;

                $SocieteDetailsCommandesProduits->save();
            }

            return response()->json([
                'message' => 'Migration des détails de produit effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des détails de produit', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des détails de produit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function allMigrationsDepenses(Request $request)
    {
        try {
            DB::beginTransaction(); // Démarrer la transaction

            Depenses::chunk(100, function ($depenses) {
                foreach ($depenses as $depense) {

                    if ($depense->id_hotel && !SocieteHotel::where('id_hotel', $depense->id_hotel)->exists()) {
                        Log::warning('id_hotel introuvable ou id_reservation vide', [
                            'id_hotel' => $depense->id_hotel
                        ]);
                        continue;
                    }
                    if (!$depense->id_hotel) {
                        continue;
                    }
                    // Vérifier si l'utilisateur existe
                    $recupererIdUser = SocieteUser::where('username', $depense->save_by)
                        ->where('id_hotel', $depense->id_hotel)
                        ->first();

                    $cat = ($depense->categorie == '1') ? "hotel" : "autres";


                    SocieteDepenses::insert([
                        'categorie' => $cat,
                        'description' => $depense->description,
                        'montant' => $depense->montant,
                        'save_by' => $recupererIdUser?->id,
                        'id_hotel' => $depense->id_hotel,
                        'created_at' => $depense->date_save,
                    ]);
                }
            });

            DB::commit();

            Log::info("Migration des dépenses terminée avec succès.");

            return response()->json(['message' => 'Migration effectuée avec succès.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur lors de la migration des dépenses : " . $e->getMessage());

            return response()->json(['error' => 'Erreur lors de la migration des dépenses.', 'details' => $e->getMessage()], 500);
        }
    }

    public function allMigrationsAccomptesCommandes(Request $request)
    {
        try {
            $accomptes = Accomptes::all();

            foreach ($accomptes as $accompte) {
                if (SocieteAccomptesCommandes::where('id', $accompte->ID)->exists()) {
                    continue; // Passer si l'acompte existe déjà
                }

                // Vérification des clés étrangères
                if ($accompte->ident_reservation && !SocieteCommande::where('id_commande', $accompte->ident_reservation)->exists()) {
                    Log::warning('Réservation introuvable', ['id_reservation' => $accompte->ident_reservation]);
                    continue;
                }

                if ($accompte->id_hotel && !SocieteHotel::where('id_hotel', $accompte->id_hotel)->exists()) {
                    Log::warning('Hôtel introuvable', ['id_hotel' => $accompte->id_hotel]);
                    continue;
                }

                $recupererIdUser = SocieteUser::where('username', $accompte->save_by)->first();

                if (!$recupererIdUser) {
                    Log::warning('Utilisateur introuvable', ['username' => $accompte->save_by]);
                    continue;
                }

                $facture = Factures::where('ident_reservation', $accompte->ident_reservation)
                    ->where('id_hotel', $accompte->id_hotel)
                    ->where('link', '!=', '')
                    ->first();

                $societeAccomptesCommande = new SocieteAccomptesCommandes();
                $societeAccomptesCommande->id_commande = $accompte->ident_reservation;
                $societeAccomptesCommande->montant = $accompte->montant;
                $societeAccomptesCommande->save_by = $recupererIdUser->id;
                $societeAccomptesCommande->created_at = $accompte->date_save;
                $societeAccomptesCommande->id_hotel = $accompte->id_hotel;
                $societeAccomptesCommande->paid = true;

                $societeAccomptesCommande->save();

                if ($facture) {
                    $SocieteFacturesCommande = new SocieteFacturesCommandes();
                    $SocieteFacturesCommande->id_accompte = $societeAccomptesCommande->id; // **L'ID est maintenant disponible**
                    $SocieteFacturesCommande->date_facture = $facture->date_facture;
                    $SocieteFacturesCommande->user = $recupererIdUser->id;
                    $SocieteFacturesCommande->num_facture = $facture->num_facture;
                    $SocieteFacturesCommande->created_at = $facture->date_facture;
                    $SocieteFacturesCommande->link = $facture->link;
                    $SocieteFacturesCommande->id_hotel = $accompte->id_hotel;

                    $SocieteFacturesCommande->save();
                }

            }

            return response()->json([
                'message' => 'Migration des Accomptes effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des accomptes', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des Accomptes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
