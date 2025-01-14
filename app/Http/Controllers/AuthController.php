<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotUsers;
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
            // Valider l'email et le mot de passe
            $request->validate([
                'email' => 'required|email',
                'password' => 'required', // Assurez-vous que le champ de mot de passe est correct
            ]);

            // Récupérer l'utilisateur par email
            $user = HotUsers::where('email', $request->email)->first();

            if ($user && md5(sha1($request->password)) === $user->password) {
                // Si le mot de passe correspond, continuer la génération du token
                $hotel = Hotel::find($user->id_hotel);
                $nom_hotel = $hotel ? $hotel->nom_etablissement : "";
            
                $token = "zazazaza";
            
                return response()->json([
                    'message' => 'Connexion réussie',
                    'token' => $token,
                    'niveau_user' => $user->niveau_user,
                    'utilisateur' => $user,
                    'nom_etablissement' => $nom_hotel
                ]);
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
        } catch (\Exception $e) {
            // Enregistrer l'erreur pour débogage
            Log::error('Une erreur est survenue lors de la connexion', ['error' => $e->getMessage()]);

            // Répondre avec une erreur interne
            return response()->json([
                'message' => 'Une erreur est survenue pendant le processus de connexion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Méthode de déconnexion
    public function logout(Request $request)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();

            if ($user) {
                // Révoquer le token actuel
                $request->user()->currentAccessToken()->delete();

                return response()->json([
                    'message' => 'Déconnexion réussie',
                ]);
            }

            return response()->json(['message' => 'Non authentifié'], 401);
        } catch (\Exception $e) {
            // Enregistrer l'erreur pour débogage
            Log::error('Une erreur est survenue lors de la déconnexion', ['error' => $e->getMessage()]);

            // Répondre avec une erreur interne
            return response()->json([
                'message' => 'Une erreur est survenue pendant la déconnexion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
