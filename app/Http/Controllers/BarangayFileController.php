<?php

namespace App\Http\Controllers;

use App\Models\BarangayFile;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangayFileController extends Controller {
    public function store(Request $request) {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'file' => 'required|mimes:pdf,doc,docx,xlsx|max:2048',
        ]);

        $filePath = $request->file('file')->store('barangay_files', 'public');
        BarangayFile::create([
            'barangay_id' => auth()->id(),
            'report_id' => $request->report_id,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $filePath,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'File submitted successfully.');
    }

    public function download(BarangayFile $file) {
        return Storage::download('public/' . $file->file_path);
    }

    public function destroy(BarangayFile $file) {
        Storage::delete('public/' . $file->file_path);
        $file->delete();
        return back()->with('success', 'File deleted successfully.');
    }
}
