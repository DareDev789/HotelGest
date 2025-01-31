<?php

namespace App\Http\Controllers;

use App\Models\SocietePrestations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PrestationsController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllprestations(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $prestations = SocietePrestations::where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'prestations' => $prestations,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des prestations', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des prestations.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    public function updatePrestations(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'prestation' => 'required|string|max:255',
                    'prix_prestation' => "required|numeric|min:0",
                    'autre_info_prestation' => "nullable|string|max:250",
                ]);

                $prestation = SocietePrestations::findOrFail($id);

                $prestation->update([
                    'prestation' => $request->prestation,
                    'prix_prestation' => $request->prix_prestation,
                    'autre_info_prestation' => $request->autre_info_prestation,
                ]);


                return response()->json(['message' => "Prestation mis à jour avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la mise à jour du Prestation', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du prestation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function CreatePrestation(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'prestation' => 'required|string|max:255',
                    'prix_prestation' => "required|numeric|min:0",
                    'autre_info_prestation' => "nullable|string|max:250",
                ]);

                $prestation= SocietePrestations::create([
                    'prestation' => $request->prestation,
                    'prix_prestation' => $request->prix_prestation,
                    'autre_info_prestation' => $request->autre_info_prestation,
                    'id_hotel' => $user->id_hotel,
                ]);


                return response()->json(['message' => "Prestation ajouté avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de l\'ajout de la prestation', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'ajout de la prestation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deletePrestation($id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $prestation = SocietePrestations::findOrFail($id);

                $prestation->delete();

                return response()->json(['message' => "Prestation supprimée avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la suppression de la prestation', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la prestation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}