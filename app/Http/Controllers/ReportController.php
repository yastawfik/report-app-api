<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Resources\ReportResource;
use App\Models\Subreport;

class ReportController extends Controller
{
    // List all reports
    public function index()
    {

        return ReportResource::collection(
            Report::with('user')->get()
        );;
    }


 public function show($id)
{
    $report = Report::with('user', 'subreports')->findOrFail($id);
    return response()->json($report);
}

    // Store a new report


public function store(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'shift' => 'required|string',
        'datetime' => 'required|date',
        'subreports' => 'required|array|min:1',
        'subreports.*.zone' => 'required|string',  // Ensure zone is always present
        'subreports.*.brick_type' => 'required|string',
        'subreports.*.weights' => 'required|array|min:1',
        'subreports.*.weights.*' => 'numeric',
        'subreports.*.average_weight' => 'required|numeric',
    ]);

    // Create the report
    $report = Report::create([
        'user_id' => optional($request->user())->id,
        'datetime' => $validated['datetime'],
        'shift' => $validated['shift'],
        'username' => optional($request->user())->name,
    ]);

    // Save each subreport
    foreach ($validated['subreports'] as $sub) {
        $report->subreports()->create([
            'zone' => $sub['zone'],
            'brick_type' => $sub['brick_type'],
            'weights' => json_encode($sub['weights']),
            'average_weight' => $sub['average_weight'],
        ]);
    }

    return response()->json($report->load('subreports', 'user'), 201);
}
    // Update an existing report


    // Delete a report
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully.'], 200);
    }


public function download($id)
{
    $report = Report::with(['user', 'brickWeights'])->findOrFail($id);

    // Generate the filename
    $filename = 'rapport_' . $report->id . '.pdf';

    // Set the file path
    $filePath = storage_path("app/reports/{$filename}");

    // Ensure the directory exists
    if (!file_exists(dirname($filePath))) {
        mkdir(dirname($filePath), 0775, true);
    }

    // Generate PDF
    $pdf = Pdf::loadView('reports.pdfreport', compact('report'));

    // Save the PDF to the file system
    $pdf->save($filePath);

    return response()->download($filePath);
}
public function getAllReports()
{
    $reports = Report::with('user')->get(); // eager-loads the associated user
    return response()->json($reports);
}
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
}
