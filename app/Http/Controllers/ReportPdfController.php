<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportPdfController extends Controller
{
    public function download($id)
    {
        $report = Report::with('user', 'brickWeights')->findOrFail($id);

        $groupedWeights = $report->brickWeights
            ->groupBy('zone')
            ->map(function ($group): mixed {
                return $group->pluck('weight')->values()->all();
            });
            $maxRows = collect($groupedWeights)->map(function ($item) {
                return count($item);
            })->max();

        $pdf = Pdf::loadView('pdf.report', compact('report', 'groupedWeights'));
        return $pdf->download("rapport_{$report->id}.pdf");
    }
}

