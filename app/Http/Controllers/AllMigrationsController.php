<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotUsers;
use App\Models\Agences;
use App\Models\Clients;
use App\Models\Bungalows;
use App\Models\Reservations;

use App\Models\SocieteUser;
use App\Models\SocieteHotel;
use App\Models\SocieteAgence;
use App\Models\SocieteClient;
use App\Models\SocieteBungalow;
use App\Models\SocieteReservation;
use App\Models\SocieteDetailsReservation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AllMigrationsController extends Controller
{
    public function allMigrationsHotel(Request $request)
    {
        try {
            $hotels = Hotel::all();

            foreach ($hotels as $hotel) {
                $existingHotel = SocieteHotel::where('id_hotel', $hotel->id_hotel)->first();

                if (!$existingHotel) {
                    $societeHotel = new SocieteHotel();

                    $societeHotel->id_hotel = $hotel->id_hotel;
                    $societeHotel->nom_etablissement = $hotel->nom_etablissement;
                    $societeHotel->gerant_etablissement = $hotel->gerant_etablissement;
                    $societeHotel->adresse = $hotel->adresse;
                    $societeHotel->email = $hotel->email;
                    $societeHotel->site_web = $hotel->site_web;
                    $societeHotel->date_inscription = $hotel->date_inscription;
                    $societeHotel->date_expiration = $hotel->date_expiration;
                    $societeHotel->ville = $hotel->ville;
                    $societeHotel->pays = $hotel->pays;
                    $societeHotel->nom_societe = $hotel->nom_societe;

                    $societeHotel->save();
                }
            }

            return response()->json([
                'message' => 'Migration des hôtels effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des hôtels', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des hôtels.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsUsers(Request $request)
    {
        try {
            $users = HotUsers::all();

            foreach ($users as $user) {
                $existinguser = SocieteUser::where('id', $user->ID)->first();

                if (!$existinguser) {
                    $SocieteUser = new SocieteUser();

                    $SocieteUser->id = $user->ID;
                    $SocieteUser->username = $user->username;
                    $SocieteUser->password = $user->password;
                    $SocieteUser->email = $user->email;
                    $SocieteUser->nom = $user->nom_user;
                    $SocieteUser->id_hotel = $user->id_hotel;
                    $SocieteUser->niveau_user = $user->niveau_user;
                    $SocieteUser->validated = 1;
                    $SocieteUser->auth = $user->auth;

                    $SocieteUser->save();
                }
            }

            return response()->json([
                'message' => 'Migration des users effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des users', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des users.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsAgences(Request $request)
    {
        try {
            $agences = Agences::all();

            foreach ($agences as $agence) {
                $existingAgence = SocieteAgence::where('id', $agence->ID)->first();

                if (!$existingAgence) {
                    $SocieteAgence = new SocieteAgence();

                    $SocieteAgence->id = $agence->ID;
                    $SocieteAgence->email_agence = $agence->email_agence;
                    $SocieteAgence->telephone_agence = $agence->telephone_agence;
                    $SocieteAgence->site_web_agence = $agence->site_web_agence;
                    $SocieteAgence->nom_agence = $agence->nom_agence;
                    $SocieteAgence->id_hotel = $agence->id_hotel;
                    $SocieteAgence->autres_info_agence = $agence->autres_info_agence;

                    $SocieteAgence->save();
                }
            }

            return response()->json([
                'message' => 'Migration des agences effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des agences', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des agences.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsClients(Request $request)
    {
        try {
            $clients = Clients::all();

            foreach ($clients as $client) {
                $existingClient = SocieteClient::where('id', $client->id_client)->first();

                if (!$existingClient) {
                    $SocieteClient = new SocieteClient();

                    $SocieteClient->id = $client->ID;
                    $SocieteClient->nom_client = $client->nom_client;
                    $SocieteClient->email = $client->email;
                    $SocieteClient->telephone = $client->telephone;
                    $SocieteClient->id_hotel = $client->id_hotel;
                    $SocieteClient->autres_info_client = $client->autres_info_client;

                    $SocieteClient->save();
                }
            }

            return response()->json([
                'message' => 'Migration des clients effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des clients', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des clients.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function allMigrationsBungalows(Request $request)
    {
        try {
            $bungalows = Bungalows::where('etat_bungalow', 1)->get();

            foreach ($bungalows as $bungalow) {
                $existingBungalow = SocieteBungalow::where('id', $bungalow->ID)->first();

                if (!$existingBungalow) {
                    $SocieteBungalow = new SocieteBungalow();

                    $SocieteBungalow->id = $bungalow->ID;
                    $SocieteBungalow->designation_bungalow = $bungalow->designation_bungalow;
                    $SocieteBungalow->type_bungalow = $bungalow->type_bungalow;
                    $SocieteBungalow->num_bungalow = $bungalow->num_bungalow;
                    $SocieteBungalow->id_hotel = $bungalow->id_hotel;
                    $SocieteBungalow->tri = $bungalow->tri;

                    $SocieteBungalow->save();
                }
            }

            return response()->json([
                'message' => 'Migration des bungalows effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des bungalows', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des bungalows.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function allMigrationsReservations(Request $request)
    {
        try {
            $reservations = Reservations::select(
                'ident_reservation',
                DB::raw('MAX(id_reservation) as id_reservation'),
                DB::raw('MAX(id_client) as id_client'),
                DB::raw('MAX(id_agence) as id_agence'),
                DB::raw('MAX(id_hotel) as id_hotel'),
                DB::raw('MAX(type_client) as type_client'),
                DB::raw('MAX(remise) as remise'),
                DB::raw('MAX(taux) as taux'),
                DB::raw('MAX(tva) as tva'),
                DB::raw('MAX(devise) as devise'),
                DB::raw('MAX(notes) as notes'),
                DB::raw('MAX(taxe) as taxe'),
                DB::raw('MAX(facture) as facture'),
                DB::raw('MAX(etat_reservation) as etat_reservation')
            )
                ->where('etat_reservation', '!=', 'Annule')
                ->where('type_reser', 1)
                ->groupBy('ident_reservation')
                ->get();

            foreach ($reservations as $reservation) {
                $existingReservation = SocieteReservation::where('id_reservation', $reservation->ident_reservation)->first();

                if (!$existingReservation) {
                    $SocieteReservation = new SocieteReservation();
                    $ident_reservation = $reservation->ident_reservation;

                    // Vérification si id_client existe dans la table SocieteClient
                    if ($reservation->id_client && !SocieteClient::find($reservation->id_client)) {
                        Log::warning('Client introuvable', ['id_client' => $reservation->id_client]);
                        continue; // Passer à la réservation suivante
                    }

                    // Vérification si id_agence existe dans la table SocieteAgence
                    if ($reservation->id_agence && !SocieteAgence::find($reservation->id_agence)) {
                        Log::warning('Agence introuvable', ['id_agence' => $reservation->id_agence]);
                        continue; // Passer à la réservation suivante
                    }

                    $id_client = empty($reservation->id_client) ? null : $reservation->id_client;
                    $id_agence = empty($reservation->id_agence) ? null : $reservation->id_agence;

                    $SocieteReservation->id_reservation = $reservation->ident_reservation;
                    $SocieteReservation->id_client = $id_client;
                    $SocieteReservation->id_agence = $id_agence;
                    $SocieteReservation->id_hotel = $reservation->id_hotel;
                    $SocieteReservation->type_client = $reservation->type_client;
                    $SocieteReservation->statut_reservation = $reservation->facture;
                    $SocieteReservation->remise = $reservation->remise;
                    $SocieteReservation->taux = $reservation->taux;
                    $SocieteReservation->tva = $reservation->tva;
                    $SocieteReservation->devise = $reservation->devise;
                    $SocieteReservation->notes = $reservation->notes;
                    $SocieteReservation->taxe = $reservation->taxe;
                    $SocieteReservation->etat_reservation = $reservation->etat_reservation;

                    $SocieteReservation->save();

                    $detailsReservations = Reservations::where('ident_reservation', $ident_reservation)->get();
                    foreach ($detailsReservations as $detailReservation) {
                        $SocieteDetailsReservation = new SocieteDetailsReservation();

                        if ($detailReservation->id_bungalow && !SocieteBungalow::find($detailReservation->id_bungalow)) {
                            Log::warning('Bungalow introuvable', ['id_bungalow' => $detailReservation->id_bungalow]);
                            continue; // Passer à la réservation suivante
                        }

                        $prix_bungalow = str_replace(' ', '', $detailReservation->prix_bungalow);

                        $SocieteDetailsReservation->id_reservation = $detailReservation->ident_reservation;
                        $SocieteDetailsReservation->id_bungalow = $detailReservation->id_bungalow;
                        $SocieteDetailsReservation->type_bungalow = $detailReservation->type_bungalow;
                        $SocieteDetailsReservation->prix_bungalow = $prix_bungalow;
                        $SocieteDetailsReservation->id_hotel = $detailReservation->id_hotel;
                        $SocieteDetailsReservation->nb_personne = $detailReservation->nb_personne;
                        $SocieteDetailsReservation->date_debut = $detailReservation->date_debut;
                        $SocieteDetailsReservation->date_fin = $detailReservation->date_fin;

                        $SocieteDetailsReservation->save();
                    }
                }
            }

            return response()->json([
                'message' => 'Migration des réservations effectuée avec succès.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la migration des réservations', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la migration des réservations.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
