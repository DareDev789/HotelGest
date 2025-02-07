<?php

namespace App\Http\Controllers;

use App\Models\SocieteMenu;
use App\Models\SocieteCategorieMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SocieteMenuController extends Controller
{
    // Liste des menus
    public function getAllMenu()
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
            return response()->json(SocieteMenu::with('categorie')->where('id_hotel', $user->id_hotel)->get(), 200, ['Content-Type' => 'application/json; charset=UTF-8']);

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
    public function CreateMenu(Request $request)
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
                'nom_menu' => 'required|string|max:255',
                'prix_menu' => 'required|numeric',
                'autres_info_menu' => 'nullable|string',
                // Corrected here: Dynamically assign user’s hotel ID
                'id_hotel' => 'required|integer|in:' . $user->id_hotel,
            ]);

            $menu = SocieteMenu::create($validated);

            return response()->json($menu, 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }


    // Mise à jour d'un menu
    public function updateMenu(Request $request, $id)
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
            $menu = SocieteMenu::findOrFail($id);

            if (!$menu || $menu->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'menu introuvable ou accès refusé.',
                ], 404);
            }

            $validated = $request->validate([
                'id_categorie' => 'sometimes|exists:societe_categorie_menu,id',
                'nom_menu' => 'sometimes|string|max:255',
                'prix_menu' => 'sometimes|numeric',
                'autres_info_menu' => 'sometimes|string',
            ]);
            $menu->update($validated);
            return response()->json($menu, 200, ['Content-Type' => 'application/json; charset=UTF-8']);

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
            $menu = SocieteMenu::findOrFail($id);

            if (!$menu || $menu->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'menu introuvable ou accès refusé.',
                ], 404);
            }

            $menu->delete();
            return response()->json(['message' => 'Menu supprimé avec succès'], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }
}

