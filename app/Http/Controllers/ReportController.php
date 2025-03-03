<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;
use App\Models\WeeklyReport;
use App\Models\ReportFile;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Show the report submission form.
     */


     public function index()
{
    $weeklyReports = WeeklyReport::with('user', 'reportType')->get();
    $reportFiles = ReportFile::with('user', 'reportType')->get();

    return view('admin.view-submissions', compact('weeklyReports', 'reportFiles'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'remarks' => 'nullable|string',
        'status' => 'required|in:pending,approved,rejected',
    ]);

    if ($request->has('weekly_report')) {
        $report = WeeklyReport::findOrFail($id);
    } else {
        $report = ReportFile::findOrFail($id);
    }

    $report->update([
        'remarks' => $request->remarks,
        'status' => $request->status,
    ]);

    return back()->with('success', 'Remarks updated successfully.');
}

    public function create()
    {
        $reportTypes = ReportType::all();
        return view('barangay.submit-report', compact('reportTypes'));
    }

    /**
     * Store the submitted report.
     */
    public function store(Request $request)
{
    $request->validate([
        'report_type_id' => 'required|exists:report_types,id',
        'file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:2048',
    ]);

    $reportType = ReportType::find($request->report_type_id);
    $filePath = $request->file('file')->store('reports');

    if ($reportType->frequency === 'weekly') {
        $request->validate([
            'month' => 'required|string',
            'week_number' => 'required|integer',
            'num_of_clean_up_sites' => 'required|integer',
            'num_of_participants' => 'required|integer',
            'num_of_barangays' => 'required|integer',
            'total_volume' => 'required|numeric',
        ]);

        WeeklyReport::create([
            'user_id' => Auth::id(),
            'report_type_id' => $request->report_type_id,
            'month' => $request->month,
            'week_number' => $request->week_number,
            'num_of_clean_up_sites' => $request->num_of_clean_up_sites,
            'num_of_participants' => $request->num_of_participants,
            'num_of_barangays' => $request->num_of_barangays,
            'total_volume' => $request->total_volume,
            'status' => 'pending',
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $filePath,
        ]);
    } else {
        ReportFile::create([
            'user_id' => Auth::id(),
            'report_type_id' => $request->report_type_id,
            'file_path' => $filePath,
            'status' => 'pending',
        ]);
    }

    return back()->with('success', 'Report submitted successfully.');
}
}
