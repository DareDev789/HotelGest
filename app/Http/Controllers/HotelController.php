<?php

namespace App\Http\Controllers;

use App\Models\SocieteHotel;
use App\Models\SocieteSettingFactures;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Storage;

class HotelController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHotelInfo(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $hotel = SocieteHotel::where('id_hotel', $user->id_hotel)->first();
            return response()->json([
                'success' => true,
                'hotel' => $hotel,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des informations', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des informations.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function getHotelSettingFacture(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $factureSetting = SocieteSettingFactures::where('id_hotel', $user->id_hotel)->first();
            return response()->json([
                'success' => true,
                'factureSetting' => $factureSetting,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des informations', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des informations.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function updateHotelSettingFactures(Request $request, $id)
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
            $validatedData = $request->validate([
                'entete' => 'nullable|string|max:65535',
                'footer' => 'nullable|string|max:65535',
            ]);

            $setting = SocieteSettingFactures::where('id_hotel', $user->id_hotel)->first();

            if (!$setting) {
                return response()->json(['message' => 'Facturation introuvable.'], 404);
            }

            // Mise à jour des données
            $setting->update($validatedData);

            return response()->json(['message' => "Facturation mise à jour avec succès !"], 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la facturation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de la facturation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateHotelInfo(Request $request)
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
                'email' => 'nullable|string',
                'gerant_etablissement' => 'nullable|string',
                'nom_etablissement' => 'nullable|string',
                'nom_societe' => 'nullable|string',
                'site_web' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $hotel = SocieteHotel::where('id_hotel', $user->id_hotel)->first();
            if (!$hotel) {
                return response()->json(['message' => 'Hôtel non trouvé'], 404);
            }

            $hotel->email = $request->email;
            $hotel->gerant_etablissement = $request->gerant_etablissement;
            $hotel->nom_etablissement = $request->nom_etablissement;
            $hotel->nom_societe = $request->nom_societe;
            $hotel->site_web = $request->site_web;

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
                $hotel->logo = Storage::url($logoPath);
            }

            $hotel->updated_at = Carbon::now();
            $hotel->save();

            return response()->json([
                'message' => 'Informations mises à jour avec succès',
                'hotel' => $hotel
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des informations', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour des informations.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



}