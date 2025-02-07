<?php

namespace App\Http\Controllers;

use App\Models\SocieteCommande;
use App\Models\SocieteDetailsCommandesMenus;
use App\Models\SocieteDetailsCommandesProduits;
use App\Models\SocieteDevise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocieteCommandesController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCommandes(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $commandes = SocieteCommande::with('client', 'agence', 'detailsCommandesProduits', 'detailsCommandesMenus')->where('id_hotel', $user->id_hotel)->get();

            return response()->json([
                'success' => true,
                'commandes' => $commandes,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des commandes', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des commandes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createCommandes(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'clientToShow.id' => 'required|integer|exists:societe_clients,id',

                'selectedMenus' => 'nullable|array',
                'selectedMenus.*.id' => 'required|integer|exists:societe_menu,id',
                'selectedMenus.*.nb' => 'required|integer|min:1',

                'selectedProduits' => 'nullable|array',
                'selectedProduits.*.id' => 'required|integer|exists:societe_produit,id',
                'selectedProduits.*.nb' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $id_commande = (string) Str::uuid();

            $devise = SocieteDevise::where('id_hotel', $user->id_hotel)
                ->where('type', 'autres')->first();

            $SocieteCommande = new SocieteCommande();

            $SocieteCommande->id_commande = $id_commande;
            $SocieteCommande->id_client = $request->clientToShow['id'] ?? null;
            $SocieteCommande->id_hotel = $user->id_hotel;
            $SocieteCommande->type_client = 'privee';
            $SocieteCommande->statut_reservation = 'confirme';
            $SocieteCommande->devise = $devise->devise;

            $SocieteCommande->save();

            if (!empty($request->selectedMenus)) {
                foreach ($request->selectedMenus as $menu) {
                    SocieteDetailsCommandesMenus::create([
                        'id_commande' => $id_commande,
                        'quantite' => $menu['nb'],
                        'id_menu' => $menu['id'],
                        'prix_menu' => $menu['menu']['prix_menu'],
                        'nom_menu' => $menu['menu']['nom_menu'],
                        'id_hotel' => $user->id_hotel,
                    ]);
                }
            }

            if (!empty($request->selectedProduits)) {
                foreach ($request->selectedProduits as $produit) {
                    SocieteDetailsCommandesProduits::create([
                        'id_commande' => $id_commande,
                        'quantite' => $produit['nb'],
                        'id_produit' => $produit['id'],
                        'prix_produit' => $produit['produit']['prix_vente'],
                        'nom_produit' => $produit['produit']['nom_produit'],
                        'id_hotel' => $user->id_hotel,
                        'save_by' => $user->id,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'commande' => $SocieteCommande,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la commande', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création de la commande.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
