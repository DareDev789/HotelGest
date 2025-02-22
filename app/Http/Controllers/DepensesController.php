<?php

namespace App\Http\Controllers;

use App\Models\SocieteDepenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepensesController extends Controller
{
    /**
     * Récupère les réservations pour un mois et une année spécifiques.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDepenses(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $depenses = SocieteDepenses::with('user')->where('id_hotel', $user->id_hotel)->get();
            return response()->json([
                'success' => true,
                'depenses' => $depenses,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des depenses', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des depenses.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function CreateDepenses(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'description' => 'required|string|max:255',
                    'montant' => "nullable|numeric|min:1",
                    'categorie' => 'required|string|max:255',
                ]);

                $depense=SocieteDepenses::create([
                    'description' => $request->description,
                    'montant' => $request->montant,
                    'categorie'=> $request->categorie,
                    'id_hotel' => $user->id_hotel,
                    'save_by' => $user->id,
                ]);


                return response()->json(['message' => "Depense ajouté avec succès !"], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de l\'ajout du depense', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'ajout du depense.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function DeleteDepenses(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {

                $depense = SocieteDepenses::findOrFail($id);

                if(!$depense){
                    return response()->json(['message' => "Depense non trouvé !"], 401, ['Content-Type' => 'application/json; charset=UTF-8']);
                }

                $depense->delete();


                return response()->json(['message' => "Depense effacé avec succès !"], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de l\'opération', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'opération.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}