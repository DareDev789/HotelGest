<?php

namespace App\Http\Controllers;

use App\Models\SocieteHotel;
use App\Models\SocieteSettingFactures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ], 200);
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
            ], 200);
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

            return response()->json(['message' => "Facturation mise à jour avec succès !"], 200);

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



}