<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ReportType, WeeklyReport, MonthlyReport, QuarterlyReport, SemestralReport, AnnualReport};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the report submission form.
     */
    public function index()
    {
        // Fetch all reports with their relationships
        $weeklyReports = WeeklyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $monthlyReports = MonthlyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $quarterlyReports = QuarterlyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $semestralReports = SemestralReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $annualReports = AnnualReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();

        // Compile all reports into an array
        $reports = [
            'weekly' => $weeklyReports,
            'monthly' => $monthlyReports,
            'quarterly' => $quarterlyReports,
            'semestral' => $semestralReports,
            'annual' => $annualReports,
        ];

        return view('admin.view-submissions', compact('reports'));
    }

    public function showSubmitReport()
    {
        // Get all report types and ensure we have at least one of each frequency
        $reportTypes = ReportType::all();

        // If no report types exist, create default ones
        if ($reportTypes->isEmpty()) {
            $defaultReportTypes = [
                ['name' => 'Weekly Clean-up Report', 'frequency' => 'weekly', 'deadline' => now()->addWeek()],
                ['name' => 'Monthly Progress Report', 'frequency' => 'monthly', 'deadline' => now()->addMonth()],
                ['name' => 'Quarterly Assessment Report', 'frequency' => 'quarterly', 'deadline' => now()->addMonths(3)],
                ['name' => 'Semestral Evaluation Report', 'frequency' => 'semestral', 'deadline' => now()->addMonths(6)],
                ['name' => 'Annual Summary Report', 'frequency' => 'annual', 'deadline' => now()->addYear()]
            ];

            foreach ($defaultReportTypes as $type) {
                ReportType::create($type);
            }

            $reportTypes = ReportType::all();
        }

        // Get the current user's ID
        $userId = Auth::id();

        // Fetch the submitted reports for the current user
        $weeklyReports = WeeklyReport::where('user_id', $userId)
            ->with('user', 'reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $monthlyReports = MonthlyReport::where('user_id', $userId)
            ->with('user', 'reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $quarterlyReports = QuarterlyReport::where('user_id', $userId)
            ->with('user', 'reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $semestralReports = SemestralReport::where('user_id', $userId)
            ->with('user', 'reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $annualReports = AnnualReport::where('user_id', $userId)
            ->with('user', 'reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the report type IDs that have already been submitted
        $submittedReportTypeIds = collect();
        $submittedReportTypeIds = $submittedReportTypeIds
            ->merge($weeklyReports->pluck('report_type_id'))
            ->merge($monthlyReports->pluck('report_type_id'))
            ->merge($quarterlyReports->pluck('report_type_id'))
            ->merge($semestralReports->pluck('report_type_id'))
            ->merge($annualReports->pluck('report_type_id'))
            ->unique();

        // Filter out report types that have already been submitted
        $availableReportTypes = $reportTypes->whereNotIn('id', $submittedReportTypeIds);

        $submittedReportsByFrequency = [
            'weekly' => $weeklyReports,
            'monthly' => $monthlyReports,
            'quarterly' => $quarterlyReports,
            'semestral' => $semestralReports,
            'annual' => $annualReports,
        ];

        return view('barangay.submit-report', [
            'reportTypes' => $availableReportTypes,
            'submittedReportsByFrequency' => $submittedReportsByFrequency,
            'submittedReportTypeIds' => $submittedReportTypeIds,
            'allReportTypes' => $reportTypes // Pass all report types to the view
        ]);
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
                return back()->with('success', 'Report updated successfully.');
            }
        }

        return back()->with('error', 'Report not found.');
    }

    /**
     * Show the form for submitting reports.
     */
    public function create()
    {
        $reportTypes = ReportType::all();

        // Fetch the submitted reports
        $weeklyReports = WeeklyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $monthlyReports = MonthlyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $quarterlyReports = QuarterlyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $semestralReports = SemestralReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
        $annualReports = AnnualReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();

        // Get the report type IDs that have already been submitted
        $submittedReportTypeIds = collect();
        $submittedReportTypeIds = $submittedReportTypeIds
            ->merge($weeklyReports->pluck('report_type_id'))
            ->merge($monthlyReports->pluck('report_type_id'))
            ->merge($quarterlyReports->pluck('report_type_id'))
            ->merge($semestralReports->pluck('report_type_id'))
            ->merge($annualReports->pluck('report_type_id'))
            ->unique();

        $submittedReportsByFrequency = [
            'weekly' => $weeklyReports,
            'monthly' => $monthlyReports,
            'quarterly' => $quarterlyReports,
            'semestral' => $semestralReports,
            'annual' => $annualReports,
        ];

        return view('barangay.submit-report', [
            'reportTypes' => $reportTypes,
            'submittedReportsByFrequency' => $submittedReportsByFrequency,
            'allReportTypes' => $reportTypes,
            'submittedReportTypeIds' => $submittedReportTypeIds
        ]);
    }

    public function store(Request $request)
    {
        $reportType = ReportType::find($request->report_type_id);

        $request->validate([
            'report_type_id' => 'required|exists:report_types,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:2048',
        ]);

        // Generate a unique filename
        $originalName = $request->file('file')->getClientOriginalName();
        $extension = $request->file('file')->getClientOriginalExtension();
        $fileName = time() . '_' . str_replace([' ', '(', ')'], '_', $originalName);
        $filePath = "reports/{$reportType->frequency}/{$fileName}";

        DB::beginTransaction();
        try {
            // Create directory if it doesn't exist
            Storage::disk('public')->makeDirectory("reports/{$reportType->frequency}");

            // Store file
            $stored = Storage::disk('public')->putFileAs(
                "reports/{$reportType->frequency}",
                $request->file('file'),
                $fileName
            );

            if (!$stored) {
                throw new \Exception('Failed to store the file');
            }

            // Report Data
            $reportData = [
                'user_id' => Auth::id(),
                'report_type_id' => $request->report_type_id,
                'file_name' => $originalName,
                'file_path' => $filePath,
                'status' => 'pending',
                'remarks' => null,
                'deadline' => $reportType->deadline,
            ];

            // Dynamic validation and data preparation based on report type
            $extraFields = [];
            switch ($reportType->frequency) {
                case 'weekly':
                    $extraFields = $request->validate([
                        'month' => 'required|string',
                        'week_number' => 'required|integer',
                        'num_of_clean_up_sites' => 'required|integer',
                        'num_of_participants' => 'required|integer',
                        'num_of_barangays' => 'required|integer',
                        'total_volume' => 'required|numeric',
                    ]);
                    break;
                case 'monthly':
                    $extraFields = $request->validate([
                        'month' => 'required|string',
                    ]);
                    break;
                case 'quarterly':
                    $extraFields = $request->validate([
                        'quarter_number' => 'required|integer|between:1,4',
                    ]);
                    break;
                case 'semestral':
                    $extraFields = $request->validate([
                        'sem_number' => 'required|integer|between:1,2',
                    ]);
                    break;
                case 'annual':
                    // No extra fields needed for annual reports
                    break;
                default:
                    throw new \Exception('Invalid report frequency type');
            }

            $modelMap = [
                'weekly' => WeeklyReport::class,
                'monthly' => MonthlyReport::class,
                'quarterly' => QuarterlyReport::class,
                'semestral' => SemestralReport::class,
                'annual' => AnnualReport::class,
            ];

            if (!isset($modelMap[$reportType->frequency])) {
                throw new \Exception('Invalid report type');
            }

            $model = $modelMap[$reportType->frequency];
            $report = $model::create(array_merge($reportData, $extraFields));

            DB::commit();
            return redirect()->back()->with([
                'success' => ucfirst($reportType->frequency) . ' report submitted successfully!',
                'report_id' => $report->id,
                'file_name' => $originalName
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            // Clean up the file if it was stored but the database transaction failed
            if (isset($stored) && $stored) {
                Storage::disk('public')->delete($filePath);
            }
            return back()->with('error', 'Failed to submit report: ' . $e->getMessage());
        }
    }

    /**
     * Show the view reports page.
     */
    public function view()
    {
        $userId = Auth::id();
        $reportTypes = ReportType::all();

        // Fetch all submitted reports for the current user
        $weeklyReports = WeeklyReport::where('user_id', $userId)
            ->with('reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $monthlyReports = MonthlyReport::where('user_id', $userId)
            ->with('reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $quarterlyReports = QuarterlyReport::where('user_id', $userId)
            ->with('reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $semestralReports = SemestralReport::where('user_id', $userId)
            ->with('reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        $annualReports = AnnualReport::where('user_id', $userId)
            ->with('reportType')
            ->orderBy('created_at', 'desc')
            ->get();

        // Combine all reports into a single collection
        $allReports = collect()
            ->concat($weeklyReports)
            ->concat($monthlyReports)
            ->concat($quarterlyReports)
            ->concat($semestralReports)
            ->concat($annualReports)
            ->sortByDesc('created_at');

        return view('barangay.view-reports', [
            'reports' => $allReports,
            'reportTypes' => $reportTypes
        ]);
    }

    /**
     * Show the resubmit form for a specific report.
     */
    public function showResubmit($id)
    {
        $report = $this->findReport($id);
        if (!$report) {
            return back()->with('error', 'Report not found.');
        }

        $reportTypes = ReportType::all();
        return view('barangay.resubmit-report', compact('report', 'reportTypes'));
    }

    /**
     * Handle report resubmission.
     */
    public function resubmit(Request $request, $id)
    {
        $report = $this->findReport($id);
        if (!$report) {
            return back()->with('error', 'Report not found.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:2048',
        ]);

        $fileName = time() . '_' . str_replace([' ', '(', ')'], '_', $request->file('file')->getClientOriginalName());
        $filePath = "reports/{$report->reportType->frequency}/{$fileName}";

        DB::beginTransaction();
        try {
            // Store file
            Storage::disk('public')->putFileAs(
                "reports/{$report->reportType->frequency}",
                $request->file('file'),
                $fileName
            );

            // Update report
            $report->update([
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $filePath,
                'status' => 'pending',
                'remarks' => null,
            ]);

            DB::commit();
            return redirect()->route('reports.view')->with('success', 'Report resubmitted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to resubmit report: ' . $e->getMessage());
        }
    }

    /**
     * Delete a report.
     */
    public function destroy($id)
    {
        $report = $this->findReport($id);
        if (!$report) {
            return response()->json(['success' => false, 'message' => 'Report not found.']);
        }

        DB::beginTransaction();
        try {
            // Delete the file
            Storage::disk('public')->delete($report->file_path);

            // Delete the report
            $report->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Report deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to delete report: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper method to find a report by ID.
     */
    private function findReport($id)
    {
        $models = [
            WeeklyReport::class,
            MonthlyReport::class,
            QuarterlyReport::class,
            SemestralReport::class,
            AnnualReport::class
        ];

        foreach ($models as $model) {
            $report = $model::find($id);
            if ($report) {
                return $report;
            }
        }

        return null;
    }

    public function overdueReports()
    {
        $reportTypes = ReportType::all();
        $overdueReports = collect();

        foreach ($reportTypes as $reportType) {
            $deadline = Carbon::parse($reportType->deadline);
            if ($deadline->isPast()) {
                $overdueReports->push($reportType);
            }
        }

        return view('barangay.overdue-reports', [
            'overdueReports' => $overdueReports
        ]);
    }
}
