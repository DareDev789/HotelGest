<?php

namespace App\Http\Controllers;

use App\Models\SocieteAgence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AgencesController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllAgences(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $agences = SocieteAgence::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'agences' => $agences,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des agences', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des agences.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    public function updateAgence(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'email_agence' => 'nullable|string|max:255',
                    'telephone_agence' => "nullable|string|max:250",
                    'site_web_agence' => "nullable|string|max:250",
                    'nom_agence' => "required|string|max:250",
                    'autres_info_agence' => "nullable|string|max:250",
                    'bg_color' => "nullable|string|max:250",
                    'text_color' => "nullable|string|max:250",
                ]);

                $agence = SocieteAgence::findOrFail($id);

                $agence->update([
                    'email_agence' => $request->email_agence,
                    'telephone_agence' => $request->telephone_agence,
                    'site_web_agence' => $request->site_web_agence,
                    'nom_agence' => $request->nom_agence,
                    'autres_info_agence' => $request->autres_info_agence,
                    'bg_color' => $request->bg_color,
                    'text_color' => $request->text_color,
                ]);


                return response()->json(['message' => "Agence mis à jour avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la mise à jour de l\' Agence', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de l\' Agence.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}