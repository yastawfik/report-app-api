<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Resources\ReportResource;

class ReportController extends Controller
{
    // List all reports
    public function index()
    {

        return ReportResource::collection(
            Report::with('user')->get()
        );;
    }

    // Show a single report
    public function show($id)
    {
        $report = Report::findOrFail($id);
        return response()->json($report, 200);
    }

    // Store a new report

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone' => 'required|string',
            'brick_type' => 'required|string',
            'weights' => 'required|array',
            'weights.*' => 'numeric',
            'average_weight' => 'required|numeric', // ✅ update this
            'datetime' => 'required|date',
            'shift' => 'required|string',
        ]);

        $report = Report::create([
            'user_id' => optional($request->user())->id,
            'zone' => $validated['zone'],
            'brick_type' => $validated['brick_type'],
            'weights' => json_encode($validated['weights']),
            'average_weight' => $validated['average_weight'], // ✅ correct field
            'datetime' => $validated['datetime'],
            'username' => optional($request->user())->name,
            'shift' => $request->shift, // ✅ save shift
        ]);
        $report->load('user');

        return response()->json($report, 201);



    }
    // Update an existing report
public function update(Request $request, Report $report)
{
    $validated = $request->validate([
        'zone' => 'required|string',
        'brick_type' => 'required|string',
        'weights' => 'required|array',
        'average_weight' => 'required|numeric',

    ]);

    $report->update([
        'zone' => $validated['zone'],
        'brick_type' => $validated['brick_type'],
        'weights' => json_encode($validated['weights']),
        'average_weight' => $validated['average_weight'],
        'shift' => $validated['shift'],
    ]);

    return response()->json(['message' => 'Report updated successfully']);
}

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

}
