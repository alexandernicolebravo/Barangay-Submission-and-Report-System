<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ReportType, WeeklyReport, MonthlyReport, QuarterlyReport, SemestralReport, AnnualReport};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the report submission form.
     */
    public function index()
    {
        return view('admin.view-submissions');
    }

public function showSubmitReport()
{
    $reportTypes = ReportType::all(); // Assuming ReportType is your model

    // Fetch the submitted reports, similar to your previous example
    $weeklyReports = WeeklyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
    $monthlyReports = MonthlyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
    $quarterlyReports = QuarterlyReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
    $semestralReports = SemestralReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();
    $annualReports = AnnualReport::with('user', 'reportType')->orderBy('created_at', 'desc')->get();

    $submittedReportsByFrequency = [
        'weekly' => $weeklyReports,
        'monthly' => $monthlyReports,
        'quarterly' => $quarterlyReports,
        'semestral' => $semestralReports,
        'annual' => $annualReports,
    ];

    return view('barangay.submit-report', compact('reportTypes', 'submittedReportsByFrequency')); // Make sure to pass it here
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
        return view('barangay.submit-report', compact('reportTypes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'report_type_id' => 'required|exists:report_types,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:2048',
        ]);

        $reportType = ReportType::find($request->report_type_id);

        $fileName = time() . '_' . str_replace([' ', '(', ')'], '_', $request->file('file')->getClientOriginalName());
        $filePath = 'reports/' . $fileName;

        DB::beginTransaction();
        try {
            // Store the file using Laravel's storage facade
            Storage::disk('local')->putFileAs('reports', $request->file('file'), $fileName);

            $reportData = [
                'user_id' => Auth::id(),
                'report_type_id' => $request->report_type_id,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'file_path' => $filePath,
                'status' => 'pending',
                'remarks' => null,
                'deadline' => now()->addDays(7),
            ];

            switch ($reportType->frequency) {
                case 'weekly':
                    $request->validate([
                        'month' => 'required|string',
                        'week_number' => 'required|integer',
                        'num_of_clean_up_sites' => 'required|integer',
                        'num_of_participants' => 'required|integer',
                        'num_of_barangays' => 'required|integer',
                        'total_volume' => 'required|numeric',
                    ]);
                    WeeklyReport::create(array_merge($reportData, [
                        'month' => $request->month,
                        'week_number' => $request->week_number,
                        'num_of_clean_up_sites' => $request->num_of_clean_up_sites,
                        'num_of_participants' => $request->num_of_participants,
                        'num_of_barangays' => $request->num_of_barangays,
                        'total_volume' => $request->total_volume,
                    ]));
                    break;
                case 'monthly':
                    $request->validate(['month' => 'required|string']);
                    MonthlyReport::create(array_merge($reportData, ['month' => $request->month]));
                    break;
                case 'quarterly':
                    $request->validate(['quarter_number' => 'required|integer']);
                    QuarterlyReport::create(array_merge($reportData, ['quarter_number' => $request->quarter_number]));
                    break;
                case 'semestral':
                    $request->validate(['sem_number' => 'required|integer']);
                    SemestralReport::create(array_merge($reportData, ['sem_number' => $request->sem_number]));
                    break;
                case 'annual':
                    AnnualReport::create($reportData);
                    break;
                default:
                    return back()->with('error', 'Invalid report type.');
            }

            DB::commit();
            return back()->with('success', 'Report submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while submitting the report: ' . $e->getMessage());
        }
    }

}
