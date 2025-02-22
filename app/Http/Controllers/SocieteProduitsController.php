<?php

namespace App\Http\Controllers;

use App\Models\SocieteDetailsCommandesProduits;
use App\Models\SocieteProduit;
use App\Models\SocieteProduitStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SocieteProduitsController extends Controller
{
    // Liste des menus
    public function getAllProduits()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $user = Auth::user();

            // Vérifier si l'utilisateur est authentifié et associé à un hôtel
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }
            return response()->json(SocieteProduit::with('categorie')->where('id_hotel', $user->id_hotel)->get(), 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récuperation des données', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récuperation des données.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    // Création d'un menu
    public function CreateProduit(Request $request)
    {
        try {
            $user = Auth::user();

            // Vérifier si l'utilisateur est authentifié et associé à un hôtel
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validated = $request->validate([
                'id_categorie' => 'required|exists:societe_categorie_menu,id',
                'nom_produit' => 'required|string|max:255',
                'prix_vente' => 'required|numeric',
                'quantifie' => 'required|boolean',
                'stock' => 'nullable|numeric',
                'prix_achat' => 'nullable|numeric',
            ]);

            // Créer le produit
            $produit = SocieteProduit::create([
                'id_categorie' => $validated['id_categorie'],
                'nom_produit' => $validated['nom_produit'],
                'prix_vente' => $validated['prix_vente'],
                'quantifie' => $validated['quantifie'],
                'id_hotel' => $user->id_hotel,
            ]);

            if ($validated['quantifie']) {
                if (isset($validated['stock']) && is_numeric($validated['stock'])) {
                    SocieteProduitStock::create([
                        'id_produit' => $produit->id,
                        'stock' => $validated['stock'],
                        'prix_achat' => $validated['prix_achat'] ?? 0,
                        'id_hotel' => $user->id_hotel,
                        'id_user' => $user->id,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le stock est requis pour les produits quantifiables.',
                    ], 400);
                }
            }

            return response()->json($produit, 201);
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.',
            ], 500);
        }
    }

    public function updateProduit(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Vérifier si l'utilisateur est authentifié et associé à un hôtel
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }
            $produit = SocieteProduit::findOrFail($id);

            if (!$produit || $produit->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'produit introuvable ou accès refusé.',
                ], 404);
            }

            $validated = $request->validate([
                'id_categorie' => 'sometimes|exists:societe_categorie_produit,id',
                'nom_produit' => 'sometimes|string|max:255',
                'prix_vente' => 'sometimes|numeric',
                'quantifie' => 'required|boolean'
            ]);
            $produit->update($validated);
            return response()->json($produit, 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateCategorieProduit(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->id_hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
            ], 401);
        }

        $validated = $request->validate([
            'idProduit' => 'required|exists:societe_produit,id',
            'id_categorie' => 'required|exists:societe_categorie_produit,id',
        ]);

        $produit = SocieteProduit::findOrFail($validated['idProduit']);

        // Vérifier si un produit avec le même nom existe déjà dans cette catégorie
        $produitExistant = SocieteProduit::where('id_hotel', $user->id_hotel)
            ->where('id_categorie', $validated['id_categorie'])
            ->where('nom_produit', $produit->nom_produit)
            ->first();

        if ($produitExistant) {
            DB::beginTransaction();

            try {
                SocieteProduitStock::where('id_produit', $produit->id)
                    ->where('id_hotel', $user->id_hotel)
                    ->update(['id_produit' => $produitExistant->id]);

                SocieteDetailsCommandesProduits::where('id_produit', $produit->id)
                    ->where('id_hotel', $user->id_hotel)
                    ->update(['id_produit' => $produitExistant->id]);

                $produit->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Produits fusionnés avec succès.',
                    'produit_fusionne' => $produitExistant
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la fusion des produits : ' . $e->getMessage(),
                ], 500);
            }
        } else {
            $produit->update(['id_categorie' => $validated['id_categorie']]);

            return response()->json([
                'success' => true,
                'message' => 'Produit déplacé avec succès.',
                'produit' => $produit
            ]);
        }
    }


    // Suppression d'un menu (Soft Delete)
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            // Vérifier si l'utilisateur est authentifié et associé à un hôtel
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }
            $produit = SocieteProduit::findOrFail($id);

            if (!$produit || $produit->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'produit introuvable ou accès refusé.',
                ], 404);
            }

            $produit->delete();
            return response()->json(['message' => 'produit supprimé avec succès'], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }
}

