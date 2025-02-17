<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangayFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ClusterForm;
class ClusterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = ClusterForm::all();
         // Fetch the file submission forms for all barangays
        // assuming the 'barangay_forms' is a table with file submission form details
        $files = BarangayFile::all(); // Or modify to fetch specific barangays
        return view('cluster.dashboard', compact('forms','files'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('cluster.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'accepted_file_types' => 'nullable|string', // file types
            'max_file_size' => 'nullable|integer', // max size in KB
        ]);

        // Create the form with file type and size restrictions
        ClusterForm::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'accepted_file_types' => $request->accepted_file_types,
            'max_file_size' => $request->max_file_size,
        ]);

        return back()->with('success', 'File submission form added successfully.');
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
        $form = ClusterForm::findOrFail($id);
        $form->delete();

        return back()->with('success', 'File submission form deleted successfully.');
    }

    public function submitFile(Request $request)
{
    $request->validate([
        'file' => 'required|file|max:' . $this->getMaxFileSize($request->form_id),
        'form_id' => 'required|exists:cluster_forms,id',
    ]);

    // Retrieve the Cluster form settings
    $form = ClusterForm::findOrFail($request->form_id);

    // Validate file type based on what the Cluster user defined
    $acceptedTypes = explode(',', $form->accepted_file_types);
    $request->file('file')->validate([
        'mimes' => implode(',', $acceptedTypes),
    ]);

    $filePath = $request->file('file')->store('barangay_files', 'public');

    BarangayFile::create([
        'user_id' => auth()->id(),
        'form_id' => $request->form_id,
        'file_name' => $request->file('file')->getClientOriginalName(),
        'file_path' => $filePath,
    ]);

    return back()->with('success', 'File uploaded successfully.');
}


}
