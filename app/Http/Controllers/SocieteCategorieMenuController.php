<?php

namespace App\Http\Controllers;

use App\Models\SocieteCategorieMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// Controller pour SocieteCategorieMenu
class SocieteCategorieMenuController extends Controller
{
    // Liste des catégories de menu
    public function getAllCategorieMenu()
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
            return response()->json(SocieteCategorieMenu::with('Menu')->where('id_hotel', $user->id_hotel)->get(), 200, ['Content-Type' => 'application/json; charset=UTF-8']);

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

    // Création d'une catégorie de menu
    public function CreateCategorieMenu(Request $request)
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
                'nom_categorie_menu' => 'required|string|max:255',
                'id_hotel' => 'required|integer|in:' . $user->id_hotel,
            ]);

            $categorie = SocieteCategorieMenu::create($validated);
            return response()->json($categorie, 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Mise à jour d'une catégorie de menu
    public function updateCategorieMenu(Request $request, $id)
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
            $categorie = SocieteCategorieMenu::findOrFail($id);

            if (!$categorie || $categorie->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'categorie introuvable ou accès refusé.',
                ], 404);
            }

            $validated = $request->validate([
                'nom_categorie_menu' => 'sometimes|string|max:255',
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

    // Suppression d'une catégorie de menu (Soft Delete)
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
            $categorie = SocieteCategorieMenu::findOrFail($id);

            if (!$categorie || $categorie->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'menu introuvable ou accès refusé.',
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
}
