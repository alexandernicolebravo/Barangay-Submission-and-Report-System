<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{WeeklyReport, MonthlyReport, QuarterlyReport, SemestralReport, AnnualReport, ReportType, Report};
use Illuminate\Support\Facades\Auth;

class BarangayController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        // Get all reports for the current user
        $weeklyReports = WeeklyReport::where('user_id', $userId)->get();
        $monthlyReports = MonthlyReport::where('user_id', $userId)->get();
        $quarterlyReports = QuarterlyReport::where('user_id', $userId)->get();
        $semestralReports = SemestralReport::where('user_id', $userId)->get();
        $annualReports = AnnualReport::where('user_id', $userId)->get();

        // Combine all reports
        $allReports = collect()
            ->concat($weeklyReports)
            ->concat($monthlyReports)
            ->concat($quarterlyReports)
            ->concat($semestralReports)
            ->concat($annualReports);

        // Calculate statistics
        $totalReports = $allReports->count();
        $approvedReports = $allReports->where('status', 'approved')->count();
        $pendingReports = $allReports->where('status', 'pending')->count();
        $rejectedReports = $allReports->where('status', 'rejected')->count();

        // Get recent reports (last 5)
        $recentReports = $allReports
            ->sortByDesc('created_at')
            ->take(5)
            ->load('reportType');

        // Get upcoming deadlines
        $upcomingDeadlines = ReportType::where('deadline', '>=', now())
            ->orderBy('deadline')
            ->take(5)
            ->get();

        return view('barangay.dashboard', compact(
            'totalReports',
            'approvedReports',
            'pendingReports',
            'rejectedReports',
            'recentReports',
            'upcomingDeadlines'
        ));
    }

    public function submissions()
    {
        $userId = Auth::id();
        $reports = collect();

        // Get all reports for the current user
        $weeklyReports = WeeklyReport::with('reportType')
            ->where('user_id', $userId)
            ->get();

        $monthlyReports = MonthlyReport::with('reportType')
            ->where('user_id', $userId)
            ->get();

        $quarterlyReports = QuarterlyReport::with('reportType')
            ->where('user_id', $userId)
            ->get();

        $semestralReports = SemestralReport::with('reportType')
            ->where('user_id', $userId)
            ->get();

        $annualReports = AnnualReport::with('reportType')
            ->where('user_id', $userId)
            ->get();

        // Combine all reports
        $reports = $reports->concat($weeklyReports)
            ->concat($monthlyReports)
            ->concat($quarterlyReports)
            ->concat($semestralReports)
            ->concat($annualReports)
            ->sortByDesc('created_at');

        // Get all report types for resubmission
        $allReportTypes = ReportType::all();

        return view('barangay.submissions', compact('reports', 'allReportTypes'));
    }

    public function submitReport()
    {
        $reportTypes = ReportType::all();
        return view('barangay.submit-report', compact('reportTypes'));
    }

    public function storeFile(Request $request) {
        // Implement file storage
    }

    public function downloadFile($id) {
        // Implement file download
    }

    public function viewFile($id) {
        // Implement file viewing
    }

    public function deleteFile($id) {
        // Implement file deletion
    }

    public function store(Request $request)
    {
        // This method is now handled by ReportController
        return redirect()->route('reports.submit');
    }

    public function resubmit(Request $request)
    {
        $request->validate([
            'report_id' => 'required',
            'report_type_id' => 'required|exists:report_types,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:2048'
        ]);

        // Get the report type to determine which model to use
        $reportType = ReportType::findOrFail($request->report_type_id);

        // Determine which model to use based on frequency
        $model = match($reportType->frequency) {
            'weekly' => WeeklyReport::class,
            'monthly' => MonthlyReport::class,
            'quarterly' => QuarterlyReport::class,
            'semestral' => SemestralReport::class,
            'annual' => AnnualReport::class,
            default => throw new \Exception('Invalid report frequency')
        };

        // Find the report
        $report = $model::findOrFail($request->report_id);

        // Check if the user owns the report
        if ($report->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Store the new file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('reports', $filename, 'public');

            // Update the report with new file
            $report->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'status' => 'pending',
                'remarks' => null
            ]);

            return back()->with('success', 'Report has been resubmitted successfully.');
        }

        return back()->with('error', 'Failed to resubmit report.');
    }
}
