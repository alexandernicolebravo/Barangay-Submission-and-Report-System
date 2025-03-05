<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReportType;
use App\Models\WeeklyReport;
use App\Models\MonthlyReport;
use App\Models\QuarterlyReport;
use App\Models\SemestralReport;
use App\Models\AnnualReport;

class ReportSubmissionsController extends Controller
{
    /**
     * Display all submitted reports.
     */
    public function index()
    {
        $reportTypes = ReportType::all();

        // Query all report types
        $query = [
            'weekly' => WeeklyReport::with('user', 'reportType'),
            'monthly' => MonthlyReport::with('user', 'reportType'),
            'quarterly' => QuarterlyReport::with('user', 'reportType'),
            'semestral' => SemestralReport::with('user', 'reportType'),
            'annual' => AnnualReport::with('user', 'reportType'),
        ];

        // If user is NOT admin, only show their own reports
        if (Auth::id() !== 1) {
            foreach ($query as $key => $model) {
                $query[$key] = $model->where('user_id', Auth::id());
            }
        }

        // Get ordered reports
        $submittedReportsByFrequency = [
            'weekly' => $query['weekly']->orderBy('created_at', 'desc')->get(),
            'monthly' => $query['monthly']->orderBy('created_at', 'desc')->get(),
            'quarterly' => $query['quarterly']->orderBy('created_at', 'desc')->get(),
            'semestral' => $query['semestral']->orderBy('created_at', 'desc')->get(),
            'annual' => $query['annual']->orderBy('created_at', 'desc')->get(),
        ];

        return view('admin.view-submissions', compact('reportTypes', 'submittedReportsByFrequency'));
    }

    /**
     * Update report status and remarks.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $reportModels = [
            WeeklyReport::class, MonthlyReport::class, QuarterlyReport::class, SemestralReport::class, AnnualReport::class
        ];

        foreach ($reportModels as $model) {
            $report = $model::find($id);
            if ($report) {
                $report->update([
                    'remarks' => $request->remarks,
                    'status' => $request->status,
                ]);

                return redirect()->back()->with('success', 'Report updated successfully.');
            }
        }

        return redirect()->back()->with('error', 'Report not found.');
    }
}
