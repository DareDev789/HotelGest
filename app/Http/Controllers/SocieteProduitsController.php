<?php

namespace App\Http\Controllers;

use App\Models\SocieteProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                'quantifie' => 'nullable|string',
                // Corrected here: Dynamically assign user’s hotel ID
                'id_hotel' => 'required|integer|in:' . $user->id_hotel,
            ]);

            $produit = SocieteProduit::create($validated);

            return response()->json($produit, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
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

