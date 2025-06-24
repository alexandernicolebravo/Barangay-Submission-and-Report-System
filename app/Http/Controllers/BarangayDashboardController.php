<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\WeeklyReport;
    use App\Models\ReportType;
    use Illuminate\Support\Facades\Auth;

    class BarangayDashboardController extends Controller
    {
        public function index()
        {
            // Fetch weekly reports related to the barangay
            $weeklyReports = WeeklyReport::where('user_id', Auth::id())->get();
            $weeklyReportType = ReportType::where('name', 'weekly')->first(); // Get the "weekly" report type

            return view('barangays.dashboard', compact('weeklyReports', 'weeklyReportType'));
        }

        public function storeWeeklyReport(Request $request)
        {
            $request->validate([
                'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
                'week_number' => 'required|integer|min:1|max:5',
                'num_of_clean_up_sites' => 'required|integer|min:0',
                'num_of_participants' => 'required|integer|min:0',
                'num_of_barangays' => 'required|integer|min:0',
                'total_volume' => 'required|numeric|min:0',
                'file' => 'required|file|mimes:pdf,docx,xlsx,xls,jpg,jpeg,png|max:25600',
            ]);

            $file = $request->file('file');
            $filePath = $file->store('weekly_reports', 'public');

            WeeklyReport::create([
                'user_id' => Auth::id(),
                'report_type_id' => ReportType::where('name', 'weekly')->first()->id,
                'month' => $request->month,
                'week_number' => $request->week_number,
                'num_of_clean_up_sites' => $request->num_of_clean_up_sites,
                'num_of_participants' => $request->num_of_participants,
                'num_of_barangays' => $request->num_of_barangays,
                'total_volume' => $request->total_volume,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
            ]);

            return redirect()->route('barangays.dashboard')->with('success', 'Weekly report submitted successfully.');
        }
    }
