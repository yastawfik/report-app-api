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
        );
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
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully.'], 200);
    }

    // Download PDF version of a report
    public function download($id)
    {
        $report = Report::with(['user', 'brickWeights'])->findOrFail($id);
        $filename = 'rapport_' . $report->id . '.pdf';
        $filePath = storage_path("app/reports/{$filename}");

        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }

        $pdf = Pdf::loadView('reports.pdfreport', compact('report'));
        $pdf->save($filePath);

        return response()->download($filePath);
    }

    // Get all reports (used for admin view)
    public function getAllReports()
    {
        $reports = Report::with('user')->get();
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
}
