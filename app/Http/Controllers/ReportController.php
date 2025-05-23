<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Resources\ReportResource;
use App\Models\Subreport;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ReportController extends Controller
{
    // List all reports
public function index(Request $request)
{
    $query = Report::with('user');

    // Filter by locked status
    if ($request->has('locked')) {
        $query->where('locked', $request->locked);
    }

    // Filter by date range
    if ($request->has('start') && $request->has('end')) {
        $query->whereBetween('created_at', [
            $request->start . ' 00:00:00',
            $request->end . ' 23:59:59',
        ]);
    }

    // Optional sorting
    $sortField = $request->get('sort_field', 'created_at'); // default
    $sortOrder = $request->get('sort_order', 'desc');       // default

    // Only allow certain fields to be sorted
    if (!in_array($sortField, ['created_at', 'username'])) {
        $sortField = 'created_at';
    }

    $reports = $query->orderBy($sortField, $sortOrder)->paginate(10);

    return ReportResource::collection($reports);
}

    // Show a single report with subreports
    public function show($id)
    {
        $report = Report::with('user',  'subreports')->findOrFail($id);
        if (!$report) {
        return response()->json(['message' => 'Report not found'], 404);
    }
        return response()->json($report);
    }

    // Store a new report with subreports
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shift' => 'required|string',
            'datetime' => 'required|date',
            'subreports' => 'required|array|min:1',
            'subreports.*.zone' => 'required|string',
            'subreports.*.brick_type' => 'required|string',
            'subreports.*.weights' => 'required|array|min:1',
            'subreports.*.weights.*' => 'numeric',
            'subreports.*.average_weight' => 'required|numeric',
        ]);

        $user = $request->user();

        $report = Report::create([
            'user_id' => optional($user)->id,
            'datetime' => $validated['datetime'],
            'shift' => $validated['shift'],
            'username' => optional($user)->name,
        ]);

        foreach ($validated['subreports'] as $sub) {
            $report->subreports()->create([
                'zone' => $sub['zone'],
                'brick_type' => $sub['brick_type'],
                'weights' => json_encode($sub['weights']),
                'average_weight' => $sub['average_weight'],
                'datetime' => $validated['datetime'],
                'shift' => $validated['shift'],
              'username' => optional($request->user())->name,
            ]);
        }

        return response()->json($report->load('subreports', 'user'), 201);
    }

    // Delete a report
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->locked) {
        return response()->json(['message' => 'Ce rapport ne peut plus être supprimé.'], 403);
    }
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully.'], 200);
    }

    // Download PDF version of a reportuse Barryvdh\DomPDF\Facade\Pdf;

public function download($id)
{
    $report = Report::with(['user', 'subreports'])->findOrFail($id);

    if (!$report->locked) {
        $report->locked = true;
        $report->save();
    }

    // Calculate max number of weights across all subreports
    $maxWeightCount = collect($report->subreports)->map(function ($sub) {
        $weights = is_array($sub->weights) ? $sub->weights : json_decode($sub->weights, true);
        return is_array($weights) ? count($weights) : 0;
    })->max();

    // Pass data to view
    $pdf = Pdf::loadView('report_pdf', [
        'report' => $report,
        'maxWeightCount' => $maxWeightCount
    ]);

    return $pdf->download("rapport_{$id}.pdf");
}

    // Get all reports (used for admin view)


public function allReports()
{
    $reports = Report::with(['user', 'subreports'])->get();

    return response()->json($reports);
}

    // Update an individual subreport
    public function updateSubreport(Request $request, $id)
    {
        $validated = $request->validate([
            'zone' => 'required|string',
            'brick_type' => 'required|string',
            'weights' => 'required|array',
            'average_weight' => 'required|numeric',
        ]);

        $subreport = Subreport::findOrFail($id);

        $subreport->update([
            'zone' => $validated['zone'],
            'brick_type' => $validated['brick_type'],
            'weights' => json_encode($validated['weights']),
            'average_weight' => $validated['average_weight'],
        ]);

        return response()->json(['message' => 'Subreport updated successfully']);
    }
    public function export(Request $request)
{
    $query = Report::with(['user', 'subreports']);

    if ($request->has('locked')) {
        $query->where('locked', $request->locked);
    }

    if ($request->has('start') && $request->has('end')) {
        $query->whereBetween('created_at', [
            $request->start . ' 00:00:00',
            $request->end . ' 23:59:59',
        ]);
    }

    $reports = $query->get();

    $pdf = Pdf::loadView('export_pdf', ['reports' => $reports]);

    return $pdf->download("rapport_filtré.pdf");
}
public function exportCsv(Request $request): StreamedResponse
{
    $query = Report::with(['user', 'subreports']);

    if ($request->has('locked')) {
        $query->where('locked', $request->locked);
    }

    if ($request->has('start') && $request->has('end')) {
        $query->whereBetween('created_at', [
            $request->start . ' 00:00:00',
            $request->end . ' 23:59:59',
        ]);
    }

    $reports = $query->get();

    $headers = [
        "Content-Type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=rapport_filtré.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0",
    ];

    $callback = function () use ($reports) {
        // Disable compression to avoid corrupt CSV
        if (function_exists('header_remove')) {
            header_remove('Content-Encoding');
        }
        header('Content-Encoding: none');

        $handle = fopen('php://output', 'w');

        // Write UTF-8 BOM for Excel to recognize UTF-8 encoding
        fwrite($handle, "\xEF\xBB\xBF");

        // CSV Header
        fputcsv($handle, ['ID', 'Date', 'Utilisateur', 'Verrouillé', 'Zone', 'Type de Brique', 'Poids Moyens']);

        foreach ($reports as $report) {
            foreach ($report->subreports as $sub) {
                fputcsv($handle, [
                    $report->id,
                    $report->created_at->format('Y-m-d H:i'),
                    $report->username,
                    $report->locked ? 'Oui' : 'Non',
                    $sub->zone,
                    $sub->brick_type,
                    $sub->average_weight,
                ]);
            }
        }

        fclose($handle);

        // Flush output buffers to make sure the file is sent completely
        ob_flush();
        flush();
    };

    return response()->stream($callback, 200, $headers);
}

public function exportExcel(Request $request)
{
    // Récupérer les données (par exemple, rapports verrouillés)
    $reports = Report::when($request->locked, function($query) {
        $query->where('locked', 1);
    })->get();

    // Création d'un nouveau Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ajouter les en-têtes
    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Heure');
    $sheet->setCellValue('C1', 'Utilisateur');
    $sheet->setCellValue('D1', 'Verrouillé');

    // Ajouter les données
    $row = 2;
    foreach ($reports as $report) {
        $sheet->setCellValue('A' . $row, $report->created_at->format('Y-m-d'));
        $sheet->setCellValue('B' . $row, $report->created_at->format('H:i:s'));
        $sheet->setCellValue('C' . $row, $report->user->name ?? '—');
        $sheet->setCellValue('D' . $row, $report->locked ? 'Oui' : 'Non');
        $row++;
    }

    // Préparer le writer et le contenu
    $writer = new Xlsx($spreadsheet);

    // Générer un nom de fichier
    $filename = 'rapport_' . date('Ymd_His') . '.xlsx';

    // Sauvegarder dans un buffer
    ob_start();
    $writer->save('php://output');
    $excelOutput = ob_get_clean();

    // Réponse HTTP pour téléchargement
    return response($excelOutput, 200)
        ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        ->header('Content-Disposition', "attachment; filename=\"$filename\"")
        ->header('Cache-Control', 'max-age=0');
}
}
