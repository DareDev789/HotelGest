<?php

namespace App\Http\Controllers;

use App\Models\SocieteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UtilisateursControllers extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

            $users = SocieteUser::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'users' => $users,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des données', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des données.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    public function getMyProfil(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }
            return response()->json([
                'success' => true,
                'user' => $user,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la mise à jour du client', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du client.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function CreateClients(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'nom_client' => 'required|string|max:255',
                    'adresse' => "nullable|string|max:250",
                    'email' => "nullable|string|max:250",
                    'telephone' => "nullable|string|max:250",
                    'autres_info_client' => "nullable|string|max:250",
                ]);

                $client = SocieteClient::create([
                    'nom_client' => $request->nom_client,
                    'adresse' => $request->adresse,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'autres_info_client' => $request->autres_info_client,
                    'id_hotel' => $user->id_hotel,
                ]);


                return response()->json(['message' => "client ajouté avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de l\'ajout du client', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'ajout du client.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteClient($id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $client = SocieteClient::findOrFail($id);

                $client->delete();

                return response()->json(['message' => "Client supprimé avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la suppression du Client', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du Client.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}