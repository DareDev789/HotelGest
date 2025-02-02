<?php

namespace App\Http\Controllers;

use App\Models\SocieteAccomptesReservations;
use App\Models\SocieteReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccomptesController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAccompte(Request $request)
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

            // Validation des données
            $request->validate([
                'id_reservation' => 'required|string|max:255',
                'montant' => 'required|numeric|min:0',
                'sommeAPayer' => 'required|numeric|min:0',
            ]);

            // Enregistrement du nouvel acompte
            $accompte = SocieteAccomptesReservations::create([
                'id_reservation' => $request->id_reservation,
                'montant' => $request->montant,
                'save_by' => $user->id,
                'paid' => false,
                'id_hotel' => $user->id_hotel,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Accompte enregistré avec succès !',
                'accompte' => $accompte, // Retourner l'acompte créé
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement de l\'acompte', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement de l\'accompte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function updateAccompte(Request $request, $id)
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

            // Validation des données
            $request->validate([
                'montant' => 'required|numeric|min:0',
                'sommeAPayer' => 'required|numeric|min:0',
            ]);

            // Récupérer l'acompte
            $accompte = SocieteAccomptesReservations::find($id);

            // Vérifier si l'acompte existe et appartient bien à l'hôtel de l'utilisateur
            if (!$accompte || $accompte->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accompte introuvable ou accès refusé.',
                ], 404);
            }

            $accompte->update([
                'montant' => $request->montant,
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Accompte modifié avec succès !',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'acompte', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de l\'acompte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function PaidAccompte(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $request->validate([
                'sommeAPayer' => 'required|numeric|min:0',
            ]);


            // Récupérer l'acompte
            $accompte = SocieteAccomptesReservations::find($id);

            // Vérifier si l'acompte existe et appartient bien à l'hôtel de l'utilisateur
            if (!$accompte || $accompte->id_hotel !== $user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accompte introuvable ou accès refusé.',
                ], 404);
            }

            $accompte->update([
                'paid' => true,
            ]);

            $totalAccomptePaid = SocieteAccomptesReservations::where('id_reservation', $accompte->id_reservation)
                ->sum('montant');

            // Vérifier si la somme payée couvre la totalité de la réservation
            if ($totalAccomptePaid >= $request->sommeAPayer) {
                SocieteReservation::where('id_reservation', $accompte->id_reservation)->update([
                    'statut_reservation' => 'oui',
                ]);
            }else{
                SocieteReservation::where('id_reservation', $accompte->id_reservation)->update([
                    'statut_reservation' => 'solde',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Accompte marquée comme payé avec succès !',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'acompte', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de l\'acompte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}