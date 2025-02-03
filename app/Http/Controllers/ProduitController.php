<?php

namespace App\Http\Controllers;

use App\Models\SocieteCategorieProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $categories = SocieteCategorieProduit::with([
                'Produits' => function ($query) {
                    $query->with('Stock');
                }
            ])->where('id_hotel', $user->id_hotel)->get();

            return response()->json($categories);


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
}
