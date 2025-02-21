<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangayFile;
use App\Models\ReportForm;
use Illuminate\Support\Facades\Storage;

class BarangayController extends Controller
{
    public function index()
    {
        $reports = ReportForm::all();
        $files = BarangayFile::where('barangay_id', auth()->id())->get();

        return view('barangay.dashboard', compact('reports', 'files'));
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'barangay_form_id' => 'required|exists:barangay_forms,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('barangay_files', 'public');

        BarangayFile::create([
            'barangay_id' => auth()->id(),
            'barangay_form_id' => $request->barangay_form_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
        ]);

        return redirect()->route('barangay.dashboard')->with('success', 'File uploaded successfully.');
    }

    public function downloadFile($id)
    {
        $file = BarangayFile::findOrFail($id);

        if ($file->barangay_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return Storage::download('public/' . $file->file_path, $file->file_name);
    }

    public function viewFile($id)
    {
        $file = BarangayFile::findOrFail($id);

        if ($file->barangay_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return response()->file(storage_path('app/public/' . $file->file_path));
    }

    public function deleteFile($id)
    {
        $file = BarangayFile::findOrFail($id);

        if ($file->barangay_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        Storage::delete('public/' . $file->file_path);
        $file->delete();

        return redirect()->route('barangay.dashboard')->with('success', 'File deleted successfully.');
    }
}

