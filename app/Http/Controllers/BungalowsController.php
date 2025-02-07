<?php

namespace App\Http\Controllers;

use App\Models\SocieteBungalow;
use App\Models\SocieteTypeBungalow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BungalowsController extends Controller
{
    public function getAllBungalows(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user && $user->id_hotel) {
                $bungalows = SocieteBungalow::where('id_hotel', $user->id_hotel)->with('typeBungalow')->get();
                return response()->json(['bungalows' => $bungalows], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la recuperation des bungalows', ['error' => $e->getMessage()]);

            // Répondre avec une erreur interne
            return response()->json([
                'message' => 'Une erreur est survenue pendant la recuperation des bungalows.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOneBungalow(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user && $user->id_hotel) {
                $request->validate([
                    'id' => 'required|string|max:255',
                ]);

                $bungalow = SocieteBungalow::where('ID', $request->id)->first();
                return response()->json(['bungalow' => $bungalow], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la recuperation des bungalows', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la recuperation des bungalows.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createOneBungalow(Request $request)
    {
        try {
            $user = Auth::user();

            // Vérifiez si l'utilisateur est associé à un hôtel
            if ($user && $user->id_hotel) {
                // Validation des données d'entrée
                $validated = $request->validate([
                    'designation' => 'required|string|max:255',
                    'numero' => 'nullable|string|max:50',
                    'type_bungalows' => 'required|array|min:1',
                    'type_bungalows.*.type_bungalow' => 'required|string|max:255',
                    'type_bungalows.*.prix_agence' => 'required|numeric|min:0',
                    'type_bungalows.*.prix_particulier' => 'required|numeric|min:0',
                ]);

                // Création du bungalow
                $bungalow = SocieteBungalow::create([
                    'designation_bungalow' => $validated['designation'],
                    'num_bungalow' => $validated['numero'],
                    'id_hotel' => $user->id_hotel,
                ]);

                // Ajout des types de bungalows
                foreach ($validated['type_bungalows'] as $typeBungalow) {
                    $bungalow->typeBungalow()->create([
                        'type_bungalow' => $typeBungalow['type_bungalow'],
                        'prix_agence' => $typeBungalow['prix_agence'],
                        'prix_particulier' => $typeBungalow['prix_particulier'],
                    ]);
                }

                return response()->json(['message' => "Bungalow enregistré avec succès !"], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du bungalow', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la création du bungalow.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateBungalow(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'designation' => 'required|string|max:255',
                    'numero' => "nullable|string|max:50",
                    'type_bungalows' => 'required|array|min:1',
                    'type_bungalows.*.type_bungalow' => 'required|string|max:255',
                    'type_bungalows.*.prix_agence' => 'required|numeric|min:0',
                    'type_bungalows.*.prix_particulier' => 'required|numeric|min:0',
                ]);

                $bungalow = SocieteBungalow::findOrFail($id);

                $bungalow->update([
                    'designation_bungalow' => $request->designation,
                    'num_bungalow' => $request->numero,
                ]);

                // Supprimer les anciens types associés à ce bungalow
                SocieteTypeBungalow::where('id_bungalow', $bungalow->id)->delete();

                // Ajouter les nouveaux types
                foreach ($request->input('type_bungalows') as $typeBungalow) {
                    SocieteTypeBungalow::create([
                        'id_bungalow' => $bungalow->id,
                        'type_bungalow' => $typeBungalow['type_bungalow'],
                        'prix_agence' => $typeBungalow['prix_agence'],
                        'prix_particulier' => $typeBungalow['prix_particulier'],
                    ]);
                }

                return response()->json(['message' => "Bungalow mis à jour avec succès !"], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la mise à jour du bungalow', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du bungalow.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteBungalow($id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $bungalow = SocieteBungalow::findOrFail($id);

                SocieteTypeBungalow::where('id_bungalow', $bungalow->id)->delete();

                $bungalow->delete();

                return response()->json(['message' => "Bungalow supprimé avec succès !"], 200, ['Content-Type' => 'application/json; charset=UTF-8']);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la suppression du bungalow', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du bungalow.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



}
