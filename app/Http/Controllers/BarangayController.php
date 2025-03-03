<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportType;

class BarangayController extends ReportTypeController
{
    public function submissions()
    {
        $reportTypes = ReportType::all();
        dd($reportTypes);
        return view('barangay.submissions', compact('reportTypes'));
    }
}
