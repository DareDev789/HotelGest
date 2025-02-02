<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\SocieteFactures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FacturesController extends Controller
{
    /**
     * Récupère le dernier numéro de facture pour l'année en cours.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastFactureNumber(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->id_hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non associé à un hôtel.',
                ], 401);
            }

            // Obtenir l'année en cours
            $currentYear = date('Y');

            // Récupérer le plus grand numéro de facture de l'année en cours pour l'hôtel de l'utilisateur
            $lastFacture = SocieteFactures::where('id_hotel', $user->id_hotel)
                ->whereYear('created_at', $currentYear)
                ->orderBy('num_facture', 'desc')
                ->first();

            // Déterminer le nouveau numéro de facture
            $lastFactureNumber = $lastFacture ? (int) $lastFacture->num_facture : 0;
            $newFactureNumber = $lastFactureNumber + 1;

            return response()->json([
                'success' => true,
                'numFacture' => $newFactureNumber,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du numéro de facture', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération du numéro de facture.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Génère un PDF et permet son téléchargement.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function Printfacture(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
            }

            $request->validate([
                'contenu' => 'required|string',
            ]);

            // Récupérer le contenu HTML
            $contenu = $request->input('contenu');
            $contenu = str_replace("?", " ", $contenu);

            // Créer le PDF à partir du contenu HTML
            $pdf = Pdf::loadHtml($contenu)->setPaper('A4', 'portrait');

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "factureProforma_{$timestamp}.pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de la facture', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de la génération de la facture.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function SaveFacture(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->id_hotel) {
                return response()->json(['message' => 'Aucun hôtel associé à cet utilisateur.'], 401);
            }

            $request->validate([
                'contenu' => 'required|string',
                'num_facture' => 'required|numeric'
            ]);

            // Récupérer le contenu HTML
            $contenu = $request->input('contenu');
            $contenu = str_replace("?", " ", $contenu);

            // Créer le PDF à partir du contenu HTML
            $pdf = Pdf::loadHtml($contenu)->setPaper('A4', 'portrait');

            // Obtenir l'année et le mois en cours
            $currentYear = date('Y');
            $currentMonth = date('m');

            // Définir le chemin où le PDF sera enregistré
            $filePath = $user->id_hotel . '/factures/' . $currentYear . '/' . $currentMonth . '/facture_' . $id . '.pdf';

            // Enregistrer le PDF dans le système de fichiers
            Storage::disk('public')->put($filePath, $pdf->output());

            // Créer une nouvelle entrée dans la table Factures et mettre à jour le lien
            $facture = new SocieteFactures();
            $facture->link = Storage::url($filePath);
            $facture->id_accompte = $id;
            $facture->user = $user->id;
            $facture->num_facture = $request->input('num_facture');
            $facture->id_hotel = $user->id_hotel;

            $facture->save();

            return response()->json([
                'message' => "Facture ajoutée avec succès !",
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout de la facture', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'ajout de la facture.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
