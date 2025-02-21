<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function index() {
        $reports = Report::all();
        return view('admin.reports.index', compact('reports'));
    }

    public function create() {
        return view('admin.reports.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Report::create($request->all());
        return redirect()->route('admin.reports.index')->with('success', 'Report created successfully.');
    }

    public function updateStatus(Request $request, Report $report) {
        $request->validate(['status' => 'required|in:Pending,Completed,Rejected']);
        $report->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Report status updated.');
    }
}
