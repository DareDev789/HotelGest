<?php

namespace App\Http\Controllers;

use App\Models\SocieteClient;
use App\Models\SocieteServicesDivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ServicesDivers extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllServicesDivers(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $divers = SocieteServicesDivers::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'divers' => $divers,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des services divers', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des services divers.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function deleteDiverService($id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $serviceDivers = SocieteServicesDivers::findOrFail($id);

                $serviceDivers->delete();

                return response()->json(['message' => "Service divers supprimé avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la suppression du Service divers', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du Service divers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateserviceDivers(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'designation' => 'required|string|max:255',
                    'description' => "nullable|string|max:50",
                    'prixPax' => 'required|numeric|min:1',
                ]);

                $serviceDivers = SocieteServicesDivers::findOrFail($id);

                $serviceDivers->update([
                    'designation' => $request->designation,
                    'description' => $request->description,
                    'prixPax'=> $request->prixPax,
                ]);


                return response()->json(['message' => "service divers mis à jour avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la mise à jour du service divers', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du service divers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}