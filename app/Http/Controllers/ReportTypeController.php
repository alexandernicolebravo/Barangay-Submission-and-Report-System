<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;

class ReportTypeController extends Controller
{
    public function index()
    {
        $reportTypes = ReportType::all();
        return view('admin.create-report', compact('reportTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:' . implode(',', ReportType::frequencies()),
            'deadline' => 'required|date',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'in:' . implode(',', array_keys(ReportType::availableFileTypes()))
        ]);

        $reportType = ReportType::create([
            'name' => $request->name,
            'frequency' => $request->frequency,
            'deadline' => $request->deadline,
            'allowed_file_types' => $request->allowed_file_types
        ]);

        return redirect()->route('admin.create-report')->with('success', 'Report type created successfully.');
    }

    public function update(Request $request, ReportType $reportType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:' . implode(',', ReportType::frequencies()),
            'deadline' => 'required|date',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'in:' . implode(',', array_keys(ReportType::availableFileTypes()))
        ]);

        $reportType->update([
            'name' => $request->name,
            'frequency' => $request->frequency,
            'deadline' => $request->deadline,
            'allowed_file_types' => $request->allowed_file_types
        ]);

        return redirect()->route('admin.create-report')->with('success', 'Report type updated successfully.');
    }

    public function destroy(ReportType $reportType)
    {
        $reportType->delete();
        return redirect()->route('admin.create-report')->with('success', 'Report type deleted successfully.');
    }
}

