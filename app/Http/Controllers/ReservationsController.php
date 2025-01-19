<?php 

namespace App\Http\Controllers;

use App\Models\SocieteReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservationsController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllReservations(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $month = $request->input('month');
            $year = $request->input('year');

            // Valider les paramètres reçus
            if (!is_numeric($month) || !is_numeric($year) || $month < 1 || $month > 12) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les paramètres mois et année sont invalides.',
                ], 400);
            }

            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $reservations = SocieteReservation::with(['detailsReservations', 'agence', 'client'])
                ->where('id_hotel', $user->id_hotel)
                ->whereHas('detailsReservations', function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('date_debut', [$startOfMonth, $endOfMonth])
                          ->orWhereBetween('date_fin', [$startOfMonth, $endOfMonth])
                          ->orWhere(function ($subQuery) use ($startOfMonth, $endOfMonth) {
                              $subQuery->where('date_debut', '<=', $startOfMonth)
                                       ->where('date_fin', '>=', $endOfMonth);
                          });
                })
                ->get();

            return response()->json([
                'success' => true,
                'reservations' => $reservations,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des réservations mensuelles', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des réservations.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
