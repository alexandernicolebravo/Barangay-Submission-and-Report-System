<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;

class ReportTypeController extends Controller
{
    public function index()
    {
        $reportTypes = ReportType::all();
        return view('admin.create-report', compact('reportTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|string',
            'deadline' => 'nullable|date',
        ]);

        ReportType::create([
            'name' => $request->name,
            'frequency' => $request->frequency,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('admin.create-report')->with('success', 'Report Type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|string',
            'deadline' => 'nullable|date',
        ]);

        $reportType = ReportType::findOrFail($id);
        $reportType->update([
            'name' => $request->name,
            'frequency' => $request->frequency,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('admin.create-report')->with('success', 'Report Type updated successfully.');
    }

    public function destroy($id)
    {
        $reportType = ReportType::findOrFail($id);
        $reportType->delete();

        return redirect()->route('admin.create-report')->with('success', 'Report Type deleted successfully.');
    }
}

