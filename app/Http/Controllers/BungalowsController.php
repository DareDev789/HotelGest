<?php

namespace App\Http\Controllers;

use App\Models\SocieteBungalow;
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
                $bungalows = SocieteBungalow::where('id_hotel', $user->id_hotel)->get();
                return response()->json(['bungalows' => $bungalows], 200);
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
                return response()->json(['bungalow' => $bungalow], 200);
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
            if ($user && $user->id_hotel) {
                $request->validate([
                    'designation_bungalow' => 'required|string|max:255',
                    'type_bungalow' => 'required|string',
                    'num_bungalow' => 'nullable|string',
                    'autres_info_bungalow' => 'nullable|string',
                    'prix_bungalow' => 'required|float',
                ]);

                $bungalow = SocieteBungalow::create([
                    'designation_bungalow' => $request->designation_bungalow,
                    'type_bungalow' => $request->type_bungalow,
                    'num_bungalow' => $request->num_bungalow,
                    'autres_info_bungalow' => $request->autres_info_bungalow,
                    'prix_bungalow' => $request->prix_bungalow,
                ]);
                return response()->json(['message' => "Bungalow enregistré avec succès !"], 200);
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
}
