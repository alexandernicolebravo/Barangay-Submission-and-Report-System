<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\BarangayFile;

class BarangayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $files = BarangayFile::where('user_id', auth()->id())->get();
        return view('barangay.dashboard', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:pdf,doc,docx,xlsx,png,jpg,jpeg|max:5120', // Max 5MB
    ]);

    $filePath = $request->file('file')->store('barangay_files', 'public');

    BarangayFile::create([
        'user_id' => auth()->id(),
        'file_name' => $request->file('file')->getClientOriginalName(),
        'file_path' => $filePath,
    ]);

    return back()->with('success', 'File uploaded successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $file = BarangayFile::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // Delete file from storage
        Storage::disk('public')->delete($file->file_path);

        // Delete file record from database
        $file->delete();

        return back()->with('success', 'File deleted successfully.');
    }
    public function download($id)
{
    $file = BarangayFile::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

    return Storage::disk('public')->download($file->file_path, $file->file_name);
}

public function view($id)
    {
        $file = BarangayFile::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        // Check if the file is an image or PDF, and return appropriate response
        $fileExtension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
            return response()->file(storage_path('app/public/' . $file->file_path));
        }

        return response()->json(['error' => 'File type not viewable'], 400);
    }

}
