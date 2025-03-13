<?php

namespace App\Http\Controllers;

use App\Models\SocieteAccomptesCommandes;
use App\Models\SocieteProduitStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\SocieteDepenses;
use App\Models\SocieteAccomptesReservations;

class SocieteFinanceService extends Controller
{
    /**
     * Récupère les statistique pour une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getFinancesYear(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validatedData = $request->validate([
                'selectedYear' => 'required|integer|min:2000|max:' . date('Y'),
            ]);

            $selectedYear = $validatedData['selectedYear'];

            $totalDepenses = SocieteDepenses::whereYear('created_at', $selectedYear)
                ->where('id_hotel', $user->id_hotel)
                ->get();

            $totalRentreArgentReser = SocieteAccomptesReservations::whereYear('created_at', $selectedYear)
                ->with('reservation', 'utilisateur')
                ->where('paid', true)
                ->where('id_hotel', $user->id_hotel)
                ->get();

            $totalRentreArgentCommande = SocieteAccomptesCommandes::whereYear('created_at', $selectedYear)
                ->with('Commande', 'utilisateur')
                ->where('paid', true)
                ->where('id_hotel', $user->id_hotel)
                ->get();

            $totalDepenseStock = SocieteProduitStock::whereYear('created_at', $selectedYear)
                ->where('id_hotel', $user->id_hotel)
                ->get();

            return response()->json([
                'success' => true,
                'total_depenses' => $totalDepenses,
                'totalRentreArgentReser' => $totalRentreArgentReser,
                'totalRentreArgentCommande' => $totalRentreArgentCommande,
                'totalDepenseStock' => $totalDepenseStock
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des informations financières', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des informations.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
