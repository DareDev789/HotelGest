<?php

namespace App\Http\Controllers;

use App\Models\SocieteAgence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgencesController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllAgences(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $agences = SocieteAgence::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'agences' => $agences,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des agences', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des agences.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

}