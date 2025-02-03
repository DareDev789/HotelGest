<?php

namespace App\Http\Controllers;

use App\Models\Categorie_Menu;
use App\Models\Categorie_Produit;
use App\Models\SocieteCategorieMenu;
use App\Models\SocieteCategorieProduit;
use App\Models\SocieteHotel;
use App\Models\SocieteMenu;
use App\Models\Menu;
use App\Models\Produit;
use App\Models\SocieteProduit;
use App\Models\SocieteProduitStock;
use App\Models\SocieteUser;
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
                ->groupBy('nom_produit', 'prix_vente')
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

                SocieteProduitStock::create([
                    'id_produit' => $produitMap[$produitKey],
                    'stock' => $stock->quantite_stock,
                    'prix_achat' => $stock->prix_achat,
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

}
