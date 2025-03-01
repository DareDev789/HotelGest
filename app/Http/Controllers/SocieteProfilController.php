<?php

namespace App\Http\Controllers;

use App\Models\SocieteUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Storage;

class SocieteProfilController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $user = SocieteUser::where('id_hotel', $user->id_hotel)->where('id', $user->id)->first();
            return response()->json([
                'success' => true,
                'user' => $user,
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

    public function getAllUsers(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $user = SocieteUser::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'user' => $user,
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

    public function updateUserInfo(Request $request)
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
                'nom' => 'nullable|string',
                'profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = SocieteUser::where('id_hotel', $user->id_hotel)->where('id', $user->id)->first();
            if (!$user) {
                return response()->json(['message' => 'Hôtel non trouvé'], 404);
            }

            $user->email = $request->email;
            $user->nom = $request->nom;

            if ($request->hasFile('profil')) {
                $logoPath = $request->file('profil')->store('profils', 'public');
                $user->profil = Storage::url($logoPath);
            }

            $user->updated_at = Carbon::now();
            $user->save();

            return response()->json([
                'message' => 'Informations mises à jour avec succès',
                'user' => $user
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