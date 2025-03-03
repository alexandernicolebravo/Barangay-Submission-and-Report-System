<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;

class ReportTypeController extends Controller
{
    public function index()
    {
        $reportTypes = ReportType::all();
        return view('admin.dashboard', compact('reportTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:report_types,name',
            'frequency' => 'required|in:weekly,monthly,quarterly,semestral,annual',
        ]);

        ReportType::create([
            'name' => $request->name,
            'frequency' => $request->frequency,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Report Type Created!');
    }

}
