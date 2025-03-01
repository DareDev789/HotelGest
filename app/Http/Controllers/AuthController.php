<?php

namespace App\Http\Controllers;

use App\Models\SocieteHotel;
use App\Models\SocieteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Méthode de Connexion
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = SocieteUser::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password))
            {
                $hotel = SocieteHotel::find($user->id_hotel);
                $nom_hotel = $hotel ? $hotel->nom_etablissement : "";
                
                if ($user->validated) {
                    
                    $token = $user->createToken('token-name', ['admin'])->plainTextToken;

                    return response()->json([
                        'message' => 'Connexion réussie',
                        'token' => $token,
                        'niveau_user' => $user->niveau_user,
                        'utilisateur' => $user,
                        'nom_etablissement' => $nom_hotel
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Email non verifié',
                        'niveau_user' => $user->niveau_user,
                        'utilisateur' => $user,
                        'nom_etablissement' => $nom_hotel
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la connexion', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant le processus de connexion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user) {
                $request->user()->currentAccessToken()->delete();

                return response()->json([
                    'message' => 'Déconnexion réussie',
                ]);
            }

            return response()->json(['message' => 'Non authentifié'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la déconnexion', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue pendant la déconnexion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
