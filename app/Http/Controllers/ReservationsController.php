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
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
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

            $accomptes = SocieteAccomptesReservations::with(['utilisateur', 'facture'])
                ->where('id_reservation', $id_reservation)
                ->get();

            return response()->json([
                'success' => true,
                'reservation' => $reservation,
                'detailReservation' => $detailReservation,
                'detailPrestation' => $detailPrestation,
                'accomptes' => $accomptes,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
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
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
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
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
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

    public function UpdateDetailReservation(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'type_bungalow' => 'required|string|max:250',
                'prix_bungalow' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $SocieteDetailsReservation = SocieteDetailsReservation::findOrFail($id);

            $SocieteDetailsReservation->update([
                'type_bungalow' => $request->type_bungalow,
                'prix_bungalow' => $request->prix_bungalow,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réservation modifiée avec succès.',
                'reservation' => $SocieteDetailsReservation,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function DeleteDetailReservation(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $SocieteDetailsReservation = SocieteDetailsReservation::findOrFail($id);
            $id_reservation = $SocieteDetailsReservation->id_reservation;

            // Récupérer tous les détails liés à la réservation
            $fetchDetails = SocieteDetailsReservation::where('id_reservation', $id_reservation)->get();

            // Vérifier s'il ne reste qu'un seul détail (empêche la suppression si c'est le dernier)
            if (count($fetchDetails) == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le dernier détail de réservation.',
                ], 400);
            }

            // Supprimer le détail de réservation
            $SocieteDetailsReservation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Détail de réservation effacé avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du détail de réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function AddDetailReservation(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'selectedBungalow.*.id' => 'required|integer|exists:societe_bungalows,id',
                'id_reservation' => 'required|string|exists:societe_reservations,id_reservation',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $SocieteReservation = SocieteReservation::where('id_reservation', $request->id_reservation)
                ->where('id_hotel', $user->id_hotel)
                ->first();

            if (!$SocieteReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation introuvable.',
                ], 404);
            }

            $timezone = $request->timezone ?? 'UTC';

            $firstDetail = $SocieteReservation->detailsReservations()->orderBy('date_debut')->first();

            if (!$firstDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun détail de réservation trouvé pour cette réservation.',
                ], 404);
            }

            $startDate = Carbon::parse($firstDetail->date_debut)->setTimezone($timezone);
            $endDate = Carbon::parse($firstDetail->date_fin)->setTimezone($timezone);

            $bungalowIds = collect($request->selectedBungalow)->pluck('id');

            // Vérifier en une seule requête les bungalows déjà réservés
            $unavailableBungalows = SocieteDetailsReservation::whereIn('id_bungalow', $bungalowIds)
                ->where('id_hotel', $user->id_hotel)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where('date_debut', '<', $endDate)
                        ->where('date_fin', '>', $startDate);
                })
                ->pluck('id_bungalow')
                ->toArray();

            if (!empty($unavailableBungalows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certains bungalows sont déjà réservés pour les dates sélectionnées.',
                    'unavailableBungalows' => $unavailableBungalows,
                ], 409);
            }

            // Ajouter les détails de réservation
            $details = [];
            foreach ($request->selectedBungalow as $bungalow) {
                $details[] = [
                    'id_reservation' => $request->id_reservation,
                    'id_bungalow' => $bungalow['id'],
                    'type_bungalow' => $bungalow['type'],
                    'prix_bungalow' => $bungalow['price'],
                    'id_hotel' => $user->id_hotel,
                    'date_debut' => $startDate,
                    'date_fin' => $endDate,
                ];
            }

            // Insérer les nouvelles réservations en une seule requête
            SocieteDetailsReservation::insert($details);

            return response()->json([
                'success' => true,
                'message' => 'Détail de réservation ajouté avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function UpdateReservation(Request $request, $id)
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
                'notes' => 'nullable|string|max:250',
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date|after_or_equal:startDate',
                'nbPax' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $timezone = $request->timezone ?? 'UTC';

            $SocieteReservation = SocieteReservation::findOrFail($id);

            if ($request->has('notes')) {
                $SocieteReservation->update(['notes' => $request->notes]);
            }

            $detailReservations = SocieteDetailsReservation::where('id_reservation', $id)->get();

            if ($detailReservations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun détail de réservation trouvé.',
                ], 404);
            }

            // Vérifier si des modifications doivent être faites
            $updateDetails = false;
            $date_debut = $request->filled('startDate') ? Carbon::parse($request->startDate)->setTimezone($timezone) : null;
            $date_fin = $request->filled('endDate') ? Carbon::parse($request->endDate)->setTimezone($timezone) : null;
            $nbPax = $request->has('nbPax') ? $request->nbPax : null;

            if ($date_debut || $date_fin || !is_null($nbPax)) {
                $updateDetails = true;
            }

            if ($updateDetails) {
                $idHotel = $user->id_hotel;

                $unavailableBungalows = [];
                foreach ($detailReservations as $bungalow) {
                    $isReserved = SocieteReservation::where('id_hotel', $idHotel)
                        ->where('id_reservation', '!=', $id)
                        ->whereHas('detailsReservations', function ($query) use ($date_fin, $date_debut, $bungalow) {
                            $query->where('id_bungalow', $bungalow['id'])
                                ->where('date_debut', '<', $date_fin)
                                ->where('date_fin', '>', $date_debut);
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

                foreach ($detailReservations as $detailReservation) {
                    if (!is_null($date_debut)) {
                        $detailReservation->date_debut = $date_debut;
                    }
                    if (!is_null($date_fin)) {
                        $detailReservation->date_fin = $date_fin;
                    }
                    if (!is_null($nbPax)) {
                        $detailReservation->nb_personne = $nbPax;
                    }
                    $detailReservation->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Réservation modifiée avec succès.',
                'reservation' => $SocieteReservation,
                'details' => $detailReservations,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function UpdateDetailDivers(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'designation' => 'required|string|max:250',
                'prix_jour' => 'required|numeric',
                'pack' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $SocieteDetailsDiver = SocieteDetailsReservationsDivers::findOrFail($id);

            $SocieteDetailsDiver->update([
                'designation' => $request->designation,
                'prix_jour' => $request->prix_jour,
                'pack' => $request->pack,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service divers modifiée avec succès.',
                'SocieteDetailsDiver' => $SocieteDetailsDiver,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du service Divers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function DeleteDetailDivers(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $SocieteDetailsDiver = SocieteDetailsReservationsDivers::findOrFail($id);

            $SocieteDetailsDiver->delete();

            return response()->json([
                'success' => true,
                'message' => 'Detail de service Divers effacé avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function AddDetailDivers(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'selectedDivers.*.id' => 'required|integer|exists:societe_services_divers,id',
                'id_reservation' => 'required|string|exists:societe_reservations,id_reservation',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $SocieteReservation = SocieteReservation::where('id_reservation', $request->id_reservation)
                ->where('id_hotel', $user->id_hotel)
                ->first();

            if (!$SocieteReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation introuvable.',
                ], 404);
            }

            // Ajouter les détails de réservation
            $details = [];
            foreach ($request->selectedDivers as $diver) {
                $details[] = [
                    'id_reservation' => $SocieteReservation->id_reservation,
                    'id_hotel' => $user->id_hotel,
                    'prix_jour' => $diver['diver']['prixPax'],
                    'pack' => $diver['pack'],
                    'id_diver' => $diver['diver']['id'],
                    'designation' => $diver['diver']['designation'],
                ];
            }

            SocieteDetailsReservationsDivers::insert($details);

            return response()->json([
                'success' => true,
                'message' => 'Détail de service divers ajouté avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function UpdateDetailPrestation(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'prestation' => 'required|string|max:250',
                'prix_prestation' => 'required|numeric',
                'nb_personne' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $SocieteDetailsPrestation = SocieteDetailsPrestations::findOrFail($id);

            $SocieteDetailsPrestation->update([
                'prestation' => $request->prestation,
                'prix_prestation' => $request->prix_prestation,
                'nb_personne' => $request->nb_personne,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prestation modifiée avec succès.',
                'SocieteDetailsPrestation' => $SocieteDetailsPrestation,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du Prestation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function DeleteDetailPrestations(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $SocieteDetailsPrestation = SocieteDetailsPrestations::findOrFail($id);

            $SocieteDetailsPrestation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Detail de reservation effacé avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function AddDetailPrestations(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'selectedPrestations.*.id' => 'required|integer|exists:societe_prestations,id',
                'id_reservation' => 'required|string|exists:societe_reservations,id_reservation',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $SocieteReservation = SocieteReservation::where('id_reservation', $request->id_reservation)
                ->where('id_hotel', $user->id_hotel)
                ->first();

            if (!$SocieteReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation introuvable.',
                ], 404);
            }

            // Ajouter les détails de réservation
            $details = [];
            foreach ($request->selectedPrestations as $prestation) {
                $details[] = [
                    'id_reservation' => $SocieteReservation->id_reservation,
                    'id_hotel' => $user->id_hotel,
                    'prix_prestation' => $prestation['prestation']['prix_prestation'],
                    'nb_personne' => $prestation['pax'],
                    'id_prestation' => $prestation['prestation']['id'],
                    'prestation' => $prestation['prestation']['prestation'],
                ];
            }

            SocieteDetailsPrestations::insert($details);

            return response()->json([
                'success' => true,
                'message' => 'Détails des prestations ajoutés avec succès.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la réservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
