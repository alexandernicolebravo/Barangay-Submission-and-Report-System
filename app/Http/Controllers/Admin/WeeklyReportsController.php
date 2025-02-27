<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;

class WeeklyReportsController extends Controller
{
    public function create()
    {
        return view('admin.weekly_reports.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'month' => 'required|string',
            'week_number' => 'required|integer',
            'num_of_clean_up_sites' => 'required|integer',
            'num_of_participants' => 'required|integer',
            'num_of_barangays' => 'required|integer',
            'total_volume' => 'required|integer',
            'kalinisan_file_path' => 'nullable|string', // Consider file upload handling
        ]);

        $fields = [];
        if ($request->filled('field_key') && $request->filled('field_value')) {
            $keys = $request->input('field_key');
            $values = $request->input('field_value');

            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index])) {
                    $fields[$key] = $values[$index];
                }
            }
        }

        $report = new WeeklyReport();
        $report->user_id = auth()->id();
        $report->month = $request->input('month');
        $report->week_number = $request->input('week_number');
        $report->num_of_clean_up_sites = $request->input('num_of_clean_up_sites');
        $report->num_of_participants = $request->input('num_of_participants');
        $report->num_of_barangays = $request->input('num_of_barangays');
        $report->total_volume = $request->input('total_volume');
        $report->kalinisan_file_path = $request->input('kalinisan_file_path');
        $report->fields = $fields;
        $report->save();

        return redirect()->route('admin.weekly-reports.create')->with('success', 'Weekly report created.');
    }

    // Add index, edit, update, destroy methods as needed
}
