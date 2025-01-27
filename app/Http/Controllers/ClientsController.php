<?php

namespace App\Http\Controllers;

use App\Models\SocieteClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientsController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllClients(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $clients = SocieteClient::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'clients' => $clients,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des clients', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des clients.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    public function updateClients(Request $request, $id)
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

                $client = SocieteClient::findOrFail($id);

                $client->update([
                    'nom_client' => $request->nom_client,
                    'adresse' => $request->adresse,
                    'email' => $request->email,
                    'telephone' => $request->telephone,
                    'autres_info_client' => $request->autres_info_client,
                ]);


                return response()->json(['message' => "client mis à jour avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la mise à jour du client', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du client.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}