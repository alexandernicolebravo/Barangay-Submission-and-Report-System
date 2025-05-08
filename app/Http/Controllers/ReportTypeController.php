<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;
use Illuminate\Support\Facades\Log;

class ReportTypeController extends Controller
{
    public function index()
    {
        $reportTypes = ReportType::all();
        return view('admin.create-report', compact('reportTypes'));
    }

    public function edit($id)
    {
        $reportType = ReportType::findOrFail($id);
        return response()->json($reportType);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'frequency' => 'required|string|in:' . implode(',', array_keys(ReportType::frequencies())),
                'deadline' => 'required|date',
                'allowed_file_types' => 'nullable|array',
                'allowed_file_types.*' => 'string|in:' . implode(',', array_keys(ReportType::availableFileTypes()))
            ]);

            ReportType::create($validated);

            return redirect()->route('admin.create-report')
                ->with('success', 'Report type created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating report type: ' . $e->getMessage());
            return redirect()->route('admin.create-report')
                ->with('error', 'Error creating report type. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $reportType = ReportType::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'frequency' => 'required|string|in:' . implode(',', array_keys(ReportType::frequencies())),
                'deadline' => 'required|date',
                'allowed_file_types' => 'nullable|array',
                'allowed_file_types.*' => 'string|in:' . implode(',', array_keys(ReportType::availableFileTypes()))
            ]);

            $reportType->update($validated);

            return redirect()->route('admin.create-report')
                ->with('success', 'Report type updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating report type: ' . $e->getMessage());
            return redirect()->route('admin.create-report')
                ->with('error', 'Error updating report type. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $reportType = ReportType::findOrFail($id);
            $reportType->delete();

            return redirect()->route('admin.create-report')
                ->with('success', 'Report type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting report type: ' . $e->getMessage());
            return redirect()->route('admin.create-report')
                ->with('error', 'Error deleting report type. Please try again.');
        }
    }
}

