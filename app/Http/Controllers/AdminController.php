<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clusters = User::where('role', 'cluster')->get();
        $barangays = User::where('role', 'barangay')->get();
        return view('admin.dashboard', compact('clusters', 'barangays'));
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
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6',
        'role' => 'required|in:cluster,barangay',
        'cluster_id' => 'nullable|exists:users,id', // Ensure valid cluster selection
    ]);

    // Ensure a cluster exists before creating a barangay
    if ($request->role === 'barangay') {
        $clusterExists = User::where('role', 'cluster')->exists();
        if (!$clusterExists) {
            return back()->with('error', 'A cluster must be created before adding a barangay.');
        }

        if (!$request->cluster_id) {
            return back()->with('error', 'Barangays must be assigned to a cluster.');
        }
    }

    // Create user
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'cluster_id' => $request->cluster_id,
    ]);

    return back()->with('success', ucfirst($request->role) . ' account created successfully.');
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
        $user = User::findOrFail($id);

        // Prevent deletion of clusters if barangays are linked
        if ($user->role === 'cluster') {
            $barangayExists = User::where('role', 'barangay')->exists();
            if ($barangayExists) {
                return back()->with('error', 'Cannot delete cluster while barangays exist.');
            }
        }

        $user->delete();

        return back()->with('success', ucfirst($user->role) . ' account deleted successfully.');
    }
}
