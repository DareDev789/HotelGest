<?php

namespace App\Http\Controllers;

use App\Models\SocieteCategorieProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    public function getCategoriesProduitsAvecStock()
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

            // Récupération des catégories avec leurs produits et la somme des stocks et commandes
            $categories = SocieteCategorieProduit::with([
                'Produits' => function ($query) {
                    $query->withCount([
                        'Stock as total_stock' => function ($q) {
                            $q->select(DB::raw('COALESCE(SUM(stock), 0)'));
                        },
                        'commandes as total_commandes' => function ($q) {
                            $q->select(DB::raw('COALESCE(SUM(quantite), 0)'));
                        }
                    ]);
                }
            ])->where('id_hotel', $user->id_hotel)->get();

            return response()->json($categories, 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des données', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des données.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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
            $categorie = SocieteCategorieProduit::findOrFail($id);

            if (!$categorie || $categorie->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'produit introuvable ou accès refusé.',
                ], 404);
            }

            $categorie->delete();
            return response()->json(['message' => 'Catégorie supprimée avec succès'], 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateCategorieProduit(Request $request, $id)
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
            $categorie = SocieteCategorieProduit::findOrFail($id);

            if (!$categorie || $categorie->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'categorie introuvable ou accès refusé.',
                ], 404);
            }

            $validated = $request->validate([
                'nom_categorie_produit' => 'sometimes|string|max:255',
            ]);

            $categorie->update($validated);
            return response()->json($categorie, 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function CreateCategorieProduit(Request $request)
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

            // Validation de la requête
            $validated = $request->validate([
                'nom_categorie_produit' => 'required|string|max:255',
            ]);

            // Nettoyer le nom de la catégorie (enlever les espaces et les caractères spéciaux)
            $cleanedCategoryName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($validated['nom_categorie_produit']));

            // Vérifier si la catégorie existe déjà, en tenant compte du nom nettoyé
            $existingCategory = SocieteCategorieProduit::whereRaw('LOWER(REPLACE(nom_categorie_produit, " ", "")) = ?', [$cleanedCategoryName])
                ->where('id_hotel', $user->id_hotel)
                ->first();

            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'La catégorie de produit existe déjà.',
                ], 400);
            }

            // Créer la nouvelle catégorie
            $categorie = SocieteCategorieProduit::create(
                [
                    'nom_categorie_produit' => $validated['nom_categorie_produit'],
                    'id_hotel' => $user->id_hotel
                ]
            );

            return response()->json($categorie, 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }
}
