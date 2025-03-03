<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;

class ReportTypeController extends Controller
{
    /**
     * Display the report type creation page.
     */
    public function create()
    {
        $reportTypes = ReportType::all();
        return view('admin.create-report', compact('reportTypes'));
    }

    /**
     * Store a newly created report type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:report_types',
            'frequency' => 'required|in:weekly,monthly,quarterly,semestral,annual',
        ]);

        ReportType::create([
            'name' => $request->name,
            'frequency' => $request->frequency,
            'deadline'=>$request->deadline,
        ]);

        return back()->with('success', 'Report type created successfully.');
    }

    /**
     * Remove the specified report type.
     */
    public function destroy($id)
    {
        $reportType = ReportType::findOrFail($id);
        $reportType->delete();

        return back()->with('success', 'Report type deleted successfully.');
    }
}
