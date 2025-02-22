<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocieteProduit;
use App\Models\SocieteProduitStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SocieteStockProduit extends Controller
{
    public function showStock(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }
            $stocks = SocieteProduitStock::with('User')->where('id_produit', $id)->where('id_hotel', $user->id_hotel)->get();

            return response()->json(['stocks' => $stocks], 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function DeleteStock(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $stock = SocieteProduitStock::where('id', $id)
                ->where('id_hotel', $user->id_hotel)
                ->first();

            if ($stock) {
                $stock->delete();
            }

            return response()->json(['message' => 'Stock effacé avec succès !'], 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function AddStock(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            $validated = $request->validate([
                'id_produit' => 'required|exists:societe_produit,id',
                'stock' => 'required|numeric|min:0',
                'prix_achat' => 'required|numeric',
            ]);

            $produit = SocieteProduit::findOrFail($validated['id_produit']);

            if ($produit && $produit->id_hotel != $user->id_hotel) {
                return response()->json(['success' => false, 'message' => 'Produit introuvable'], 406);
            }

            $stock = SocieteProduitStock::create([
                'id_hotel' => $user->id_hotel,
                'id_produit' => $validated['id_produit'],
                'stock' => $validated['stock'],
                'prix_achat' => $validated['prix_achat'],
                'id_user' => $user->id,
            ]);

            return response()->json(['message' => 'Stock ajouté avec succès !'], 200, ['Content-Type' => 'application/json; charset=UTF-8']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }
}