<?php

namespace App\Http\Controllers;

use App\Models\Viagens;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ViagemPdfController extends Controller
{
    public function export($id)
    {

        $viagem = Viagens::with(['viajantes', 'voos', 'pontosInteresse', 'objetivos', 'seguros', 'hotel'])
            ->findOrFail($id);

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadView('pdf.viagem', compact('viagem'));        $nomeArquivo = 'viagem_' . $viagem->pk_id_viagem . '.pdf';
        return $pdf->download($nomeArquivo);
    }
}
