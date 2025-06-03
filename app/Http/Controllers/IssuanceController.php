<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class IssuanceController extends Controller
{
    /**
     * Display a listing of issuances for admin.
     */
    public function index(Request $request)
    {
        $query = Issuance::with('uploader');

        if ($request->get('archived') === 'true') {
            $query->archived(); // Show archived issuances
        } else {
            $query->active(); // Show active issuances
        }

        $issuances = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.issuances.index', compact('issuances'));
    }

    /**
     * Store a newly created issuance.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,zip,rar'
            ], [
                'title.required' => 'The title field is required.',
                'title.max' => 'The title may not be greater than 255 characters.',
                'file.required' => 'Please select a file to upload.',
                'file.max' => 'The file size must not exceed 20MB.',
                'file.mimes' => 'The file must be a valid format (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, JPEG, PNG, ZIP, RAR).'
            ]);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('issuances', $fileName, 'public');

            $issuance = Issuance::create([
                'title' => $validated['title'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => Auth::id()
            ]);

            return redirect()->route('admin.issuances.index')
                ->with('success', 'Issuance uploaded successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error uploading issuance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to upload issuance. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified issuance.
     */
    public function show(Issuance $issuance)
    {
        return view('admin.issuances.show', compact('issuance'));
    }

    /**
     * Show the form for editing the specified issuance.
     */
    public function edit(Issuance $issuance)
    {
        return view('admin.issuances.edit', compact('issuance'));
    }

    /**
     * Update the specified issuance.
     */
    public function update(Request $request, Issuance $issuance)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,zip,rar'
            ], [
                'title.required' => 'The title field is required.',
                'title.max' => 'The title may not be greater than 255 characters.',
                'file.required' => 'Please select a file to upload.',
                'file.max' => 'The file size must not exceed 20MB.',
                'file.mimes' => 'The file must be a valid format (PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, JPEG, PNG, ZIP, RAR).'
            ]);

            // Delete old file
            Storage::disk('public')->delete($issuance->file_path);

            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('issuances', $fileName, 'public');

            $updateData = [
                'title' => $validated['title'],
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
            ];

            $issuance->update($updateData);

            return redirect()->route('admin.issuances.index')
                ->with('success', 'Issuance reuploaded successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating issuance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update issuance. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified issuance.
     */
    public function destroy(Issuance $issuance)
    {
        try {
            // Delete the file
            Storage::disk('public')->delete($issuance->file_path);

            // Delete the record
            $issuance->delete();

            return redirect()->route('admin.issuances.index')
                ->with('success', 'Issuance deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting issuance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete issuance. Please try again.');
        }
    }

    /**
     * Download the issuance file.
     */
    public function download(Issuance $issuance)
    {
        try {
            $filePath = storage_path('app/public/' . $issuance->file_path);

            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File not found.');
            }

            return response()->download($filePath, $issuance->file_name);

        } catch (\Exception $e) {
            Log::error('Error downloading issuance: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download file.');
        }
    }

    /**
     * Display issuances for barangay users.
     */
    public function barangayIndex(Request $request)
    {
        $query = Issuance::with('uploader')->active(); // Only show non-archived issuances

        // Handle sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $issuances = $query->paginate(10);

        return view('barangay.issuances.index', compact('issuances'));
    }

    /**
     * Show issuance details for barangay users.
     */
    public function barangayShow(Issuance $issuance)
    {
        return view('barangay.issuances.show', compact('issuance'));
    }

    /**
     * Archive the specified issuance.
     */
    public function archive(Issuance $issuance)
    {
        try {
            $issuance->archive();

            return redirect()->route('admin.issuances.index')
                ->with('success', 'Issuance archived successfully.');

        } catch (\Exception $e) {
            Log::error('Error archiving issuance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to archive issuance. Please try again.');
        }
    }

    /**
     * Unarchive the specified issuance.
     */
    public function unarchive(Issuance $issuance)
    {
        try {
            $issuance->unarchive();

            return redirect()->route('admin.issuances.index', ['archived' => 'true'])
                ->with('success', 'Issuance unarchived successfully.');

        } catch (\Exception $e) {
            Log::error('Error unarchiving issuance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to unarchive issuance. Please try again.');
        }
    }
}
