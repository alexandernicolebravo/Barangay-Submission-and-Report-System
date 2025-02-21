<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportSubmission;
use Illuminate\Support\Facades\Storage;
use App\Models\FileSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ReportSubmissionController extends Controller
{
    // Show the form to create a report submission portal
    public function create()
    {
        return view('admin.create-report');
    }

    // Store a new report submission portal
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ReportSubmission::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'active', // Default status
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Report submission portal created.');
    }

    // Show the submission portal for barangays
    public function index()
    {
        $submissions = ReportSubmission::where('status', 'active')->get();

        // dd($submissions);
            return view('barangay.submissions', compact('submissions'));

    }

    // Barangay submits a file


    // public function submitFile(Request $request, $id)
    // {

    //     dd($request->all());
    //     $request->validate([
    //         'file' => 'required|file|mimes:pdf,doc,docx,xlsx,png,jpg|max:2048',
    //     ]);

    //     $submission = ReportSubmission::find($id);

    //     if (!$submission) {
    //         return redirect()->back()->with('error', 'Report submission portal not found.');
    //     }

    //     // Store the file
    //     $filePath = $request->file('file')->store('barangay_reports', 'public');

    //     // Debugging
    //     Log::info("File uploaded successfully: " . $filePath);

    //     // Save to database
    //     $fileSubmission = FileSubmission::create([
    //         'report_submission_id' => $submission->id,
    //         'file_path' => $filePath,
    //         'status' => 'submitted',
    //     ]);

    //     // if ($fileSubmission) {
    //     //     Log::info("File submission saved: " . json_encode($fileSubmission));
    //     // } else {
    //     //     Log::error("Failed to save file submission.");
    //     // }

    //     return redirect()->back()->with('success', 'File submitted successfully.');
    // }

    // // Admin updates submission status
    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:completed,rejected',
    //     ]);

    //     $submission = ReportSubmission::findOrFail($id);
    //     $submission->update(['status' => $request->status]);

    //     return redirect()->back()->with('success', 'Submission status updated.');
    // }

    public function submitFile(Request $request, $id)
    {
        // Remove debugging statement to allow execution to continue
        Log::info('submitFile() hit', ['request' => $request->all()]);

        // Validate the file
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xlsx,png,jpg,jpeg|max:2048',
        ]);

        // Find the report submission entry
        $submission = ReportSubmission::find($id);
        if (!$submission) {
            return back()->with('error', 'Submission not found.');
        }

        // Store the file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('barangay_files', $fileName, 'public');

        // Save file submission in FileSubmission model
        FileSubmission::create([
            'report_submission_id' => $submission->id,
            'user_id' => auth()->id(), // Ensure the user is authenticated
            'file_path' => $filePath,
            'status' => 'submitted',
        ]);

        return back()->with('success', 'File uploaded successfully!');
    }






    // Admin views all submissions
    public function viewSubmissions()
{
    $submissions = DB::table('file_submissions')
        ->join('report_submissions', 'file_submissions.report_submission_id', '=', 'report_submissions.id')
        ->join('users', 'file_submissions.user_id', '=', 'users.id') // Fetch user details
        ->select('file_submissions.*', 'report_submissions.title', 'report_submissions.description', 'users.name as submitted_by')
        ->get();

    return view('admin.view-submissions', compact('submissions'));
}

    // Admin views a specific submission
    public function show($id)
    {
        $submission = ReportSubmission::findOrFail($id);
        return view('admin.view-submission', compact('submission'));
    }

    public function undoSubmission($id)
{
    $submission = DB::table('file_submissions')
        ->where('user_id', auth()->id())
        ->where('report_submission_id', $id)
        ->first();

    if (!$submission) {
        return back()->with('error', 'Submission not found.');
    }

    DB::table('file_submissions')
        ->where('user_id', auth()->id())
        ->where('report_submission_id', $id)
        ->update(['status' => 'pending', 'resubmittable' => true]);

    return back()->with('success', 'You can now resubmit your file.');
}

}
