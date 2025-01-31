<?php

namespace App\Http\Controllers;

use App\Models\SocieteAccomptesReservations;
use App\Models\SocieteDetailsPrestations;
use App\Models\SocieteDetailsReservationsDivers;
use App\Models\SocieteReservation;
use App\Models\SocieteDetailsReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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


    public function getOneReservation(Request $request, $id_reservation)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }


            $reservation = SocieteReservation::with(['agence', 'client', 'detailsServicesDivers'])
                ->where('id_hotel', $user->id_hotel)
                ->where('id_reservation', $id_reservation)
                ->get();

            $detailReservation = SocieteDetailsReservation::with(['bungalow'])
                ->where('id_hotel', $user->id_hotel)
                ->where('id_reservation', $id_reservation)
                ->get();

            $detailPrestation = SocieteDetailsPrestations::where('id_reservation', $id_reservation)
                ->get();

            $accomptes = SocieteAccomptesReservations::with(['user'])
                ->where('id_reservation', $id_reservation)
                ->get();

            return response()->json([
                'success' => true,
                'reservation' => $reservation,
                'detailReservation' => $detailReservation,
                'detailPrestation' => $detailPrestation,
                'accomptes' => $accomptes,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la réservation', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function ConfirmOneReservation(Request $request, $id_reservation)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            // Valider les données entrantes
            $validated = $request->validate([
                'remise' => 'required|numeric|min:0|max:100',
                'tva' => 'required|numeric|min:0|max:100',
            ]);


            $reservation = SocieteReservation::where('id_hotel', $user->id_hotel)
                ->where('id_reservation', $id_reservation)
                ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation introuvable ou non associée à cet hôtel.',
                ], 404);
            }

            $reservation->update([
                'remise' => $validated['remise'],
                'tva' => $validated['tva'],
                'statut_reservation' => 'confirme',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réservation confirmée avec succès !',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération de la réservation', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function AnnulerOneReservation($id_reservation)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $reservation = SocieteReservation::findOrFail($id_reservation);

            if (!$reservation || ($reservation && $reservation->id_hotel != $user->id_hotel)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation introuvable ou non associée à cet hôtel.',
                ], 404);
            }

            $reservation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Réservation annulé avec succès !',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'annulation de la réservation de la réservation', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'annulation de la réservation de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createReservation(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'type_client' => 'required|string',
                'nbPax' => 'required|integer|min:1',
                'selectedBungalow' => 'required|array|min:1',
                'selectedBungalow.*.id' => 'required|integer|exists:societe_bungalows,id',
                'agenceToShow.id' => 'nullable|integer|exists:societe_agences,id',
                'clientToShow.id' => 'nullable|integer|exists:societe_clients,id',

                // Validation pour les prestations (optionnelles)
                'selectedPrestations' => 'nullable|array',
                'selectedPrestations.*.id' => 'required|integer|exists:societe_prestations,id',
                'selectedPrestations.*.pax' => 'required|integer|min:1',

                // Validation pour les services divers (optionnels)
                'selectedDivers' => 'nullable|array',
                'selectedDivers.*.id' => 'required|integer|exists:societe_services_divers,id',
                'selectedDivers.*.pack' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $timezone = $request->timezone ?? 'UTC';

            $startDate = Carbon::parse($request->startDate)->setTimezone($timezone);
            $endDate = Carbon::parse($request->endDate)->setTimezone($timezone);
            $idHotel = $user->id_hotel;

            // Vérifier la disponibilité des bungalows sélectionnés
            $unavailableBungalows = [];
            foreach ($request->selectedBungalow as $bungalow) {
                $isReserved = SocieteReservation::where('id_hotel', $idHotel)
                    ->whereHas('detailsReservations', function ($query) use ($startDate, $endDate, $bungalow) {
                        $query->where('id_bungalow', $bungalow['id'])
                            ->where('date_debut', '<', $endDate)
                            ->where('date_fin', '>', $startDate);
                    })->exists();

                if ($isReserved) {
                    $unavailableBungalows[] = $bungalow;
                }
            }

            if (!empty($unavailableBungalows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certains bungalows sont déjà réservés pour les dates sélectionnées.',
                    'unavailableBungalows' => $unavailableBungalows,
                ], 409);
            }

            // Création de la réservation
            $reservation = SocieteReservation::create([
                'id_reservation' => Str::uuid(),
                'id_client' => $request->clientToShow['id'] ?? null,
                'id_agence' => $request->agenceToShow['id'] ?? null,
                'id_hotel' => $idHotel,
                'type_client' => $request->type_client,
                'reserv_par' => $user->id,
                'statut_reservation' => 'non',
                'devise' => 'MGA',
                'etat_reservation' => 'Reserve',
            ]);

            // Ajouter les détails de réservation pour les bungalows
            foreach ($request->selectedBungalow as $bungalow) {
                SocieteDetailsReservation::create([
                    'id_reservation' => $reservation->id_reservation,
                    'id_bungalow' => $bungalow['id'],
                    'type_bungalow' => $bungalow['type'],
                    'prix_bungalow' => $bungalow['price'],
                    'id_hotel' => $idHotel,
                    'nb_personne' => $request->nbPax,
                    'date_debut' => $startDate,
                    'date_fin' => $endDate,
                ]);
            }

            // Ajouter les prestations si elles existent
            if (!empty($request->selectedPrestations)) {
                foreach ($request->selectedPrestations as $prestation) {
                    SocieteDetailsPrestations::create([
                        'id_reservation' => $reservation->id_reservation,
                        'nb_personne' => $prestation['pax'],
                        'id_prestation' => $prestation['prestation']['id'],
                        'prix_prestation' => $prestation['prestation']['prix_prestation'],
                        'date_in' => $startDate,
                        'date_out' => $endDate,
                        'prestation' => $prestation['prestation']['prestation'],
                        'id_hotel' => $idHotel,
                    ]);
                }
            }

            // Ajouter les services divers si existants
            if (!empty($request->selectedDivers)) {
                foreach ($request->selectedDivers as $diver) {
                    SocieteDetailsReservationsDivers::create([
                        'id_reservation' => $reservation->id_reservation,
                        'id_hotel' => $idHotel,
                        'prix_jour' => $diver['diver']['prixPax'],
                        'pack' => $diver['pack'],
                        'id_diver' => $diver['diver']['id'],
                        'designation' => $diver['diver']['designation'],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Réservation créée avec succès.',
                'reservation' => $reservation,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
