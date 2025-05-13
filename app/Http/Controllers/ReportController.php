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
        ]);

        $report = Report::create([
            'user_id' => optional($request->user())->id,
            'zone' => $validated['zone'],
            'brick_type' => $validated['brick_type'],
            'weights' => json_encode($validated['weights']),
            'average_weight' => $validated['average_weight'], // ✅ correct field
            'datetime' => $validated['datetime'],
        ]);
        $report->load('user');

        return response()->json($report, 201);



    }
    // Update an existing report
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $report = Report::findOrFail($id);
        $report->update($validated);

        $report->weights = $request->input('weights');
         $report->save();

        return response()->json($report, 200);
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

        $pdf = PDF::loadView('reports.pdf', compact('report'));
        $filename = 'rapport_' . $report->id . '.pdf';
        $path = storage_path('app/public/' . $filename);
        $pdf->save($path);

        return response()->json([
            'url' => asset('storage/' . $filename)
        ]);
    }

}
