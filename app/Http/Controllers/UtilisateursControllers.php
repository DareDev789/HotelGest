<?php

namespace App\Http\Controllers;

use App\Models\SocieteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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


    public function ChangePassword(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $request->validate([
                    'oldPassword' => 'required|string|max:250',
                    'newPassword' => "required|string|min:6|max:250",
                ]);
                if ($user && Hash::check($request->oldPassword, $user->password)) {
                    $user->password = $request->newPassword;
                    $user->save();
                    return response()->json(['message' => "Mot de passe modifié avec succès !"], 200);
                }
                return response()->json(['message' => 'L\'ancien mot de passe est incorrect.'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la modification du mot de passe', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la modification du mot de passe.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function MakePassword(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email' => "required|string",
    //             'password' => "required|string|min:6|max:250",
    //         ]);
    //         $compte = SocieteUser::where('email', $request->email)->first();

    //         $compte->password = $request->password;
    //         $compte->save();

    //         return response()->json([
    //             'message' => "Mot de passe modifié avec succès !",
    //             'compte'=>$compte
    //         ], 200);
            
    //     } catch (\Exception $e) {
    //         Log::error('Une erreur est survenue lors de la modification du mot de passe', ['error' => $e->getMessage()]);

    //         return response()->json([
    //             'message' => 'Une erreur est survenue lors de la modification du mot de passe.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function deleteUser($id)
    {
        try {
            $user = Auth::user();

            if ($user && $user->id_hotel) {
                $user = SocieteUser::findOrFail($id);

                if ($user->id == $id) {
                    return response()->json(['message' => 'Vous n\'avez pas le droit nécessaire pour supprimer votre compte'], 401);
                }

                if ($user->niveau_user != 3) {
                    return response()->json(['message' => 'Vous n\'avez pas le droit nécessaire pour supprimer cet utilisateur'], 401);
                }
                $user->delete();

                return response()->json(['message' => "utilisateur supprimé avec succès !"], 200);
            }

            return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
        } catch (\Exception $e) {
            Log::error('Une erreur est survenue lors de la suppression de l\'utilisateur', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de l\'utilisateur.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}