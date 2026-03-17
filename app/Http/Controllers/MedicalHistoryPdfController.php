<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Veterinary;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalHistoryPdfController extends Controller
{
    public function download(Veterinary $veterinary, Pet $pet, $token)
    {
        // Security check
        if ($pet->public_token !== $token || $pet->veterinary_id !== $veterinary->id || $veterinary->subscription_status === \App\Enums\SubscriptionStatus::CANCELLED) {
            abort(404);
        }

        // Plan check
        if ($veterinary->plan !== 'pro') {
            abort(403, 'Esta función solo está disponible para el Plan PRO.');
        }

        $pet->load([
            'customer',
            'species',
            'breed',
            'medicalRecords' => function ($query) {
                $query->with('types')->orderByDesc('performed_at');
            },
        ]);

        $pdf = Pdf::loadView('pdf.pet-history', [
            'veterinary' => $veterinary,
            'pet' => $pet,
        ]);

        $filename = "Historial_{$pet->name}_".now()->format('dmY').'.pdf';

        return $pdf->download($filename);
    }
}
