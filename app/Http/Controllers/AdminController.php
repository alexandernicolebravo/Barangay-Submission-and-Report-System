<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with users list.
     */
    public function index()
    {
        $clusters = User::where('role', 'cluster')->get();
        $barangays = User::where('role', 'barangay')->get();
        $users = User::all(); // Fetch all users
        return view('admin.dashboard', compact('clusters', 'barangays', 'users'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:cluster,barangay',
            'cluster_id' => 'nullable|exists:users,id',
        ]);

        if ($request->role === 'barangay') {
            $clusterExists = User::where('role', 'cluster')->exists();
            if (!$clusterExists) {
                return back()->with('error', 'A cluster must be created before adding a barangay.');
            }

            if (!$request->cluster_id) {
                return back()->with('error', 'Barangays must be assigned to a cluster.');
            }
        }

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
     * Confirm deactivation of a user.
     */
    public function confirmDeactivation($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'cluster') {
            $barangayExists = User::where('role', 'barangay')
                ->where('cluster_id', $user->id)
                ->exists();

            if ($barangayExists) {
                return response()->json([
                    'confirm' => $user->is_active
                        ? 'This cluster has assigned barangays. Are you sure you want to deactivate it?'
                        : 'This cluster has assigned barangays. Are you sure you want to reactivate it?'
                ]);
            }
        }

        return response()->json([
            'confirm' => $user->is_active
                ? 'Are you sure you want to deactivate this barangay?'
                : 'Are you sure you want to reactivate this barangay?'
        ]);
    }

    /**
     * Deactivate or activate a user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', ucfirst($user->role) . ' status updated to ' . ($user->is_active ? 'active' : 'inactive') . '.');
    }
}
