<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements.
     */
    public function index()
    {
        $announcements = Announcement::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        // Custom validation logic based on whether image is uploaded
        $rules = [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'background_color' => 'nullable|string|max:10',
            'priority' => 'nullable|integer|min:0|max:10',
        ];

        // If no image is uploaded, require title, content, and category
        if (!$request->hasFile('image')) {
            $rules['title'] = 'required|string|max:255';
            $rules['content'] = 'required|string';
            $rules['category'] = 'required|string|in:announcement,recognition,important_update,upcoming_event';
        } else {
            // If image is uploaded, these fields are optional
            $rules['title'] = 'nullable|string|max:255';
            $rules['content'] = 'nullable|string';
            $rules['category'] = 'nullable|string|in:announcement,recognition,important_update,upcoming_event';
        }

        $validator = Validator::make($request->all(), $rules);

        // Add custom validation message
        $validator->after(function ($validator) use ($request) {
            if (!$request->hasFile('image') && (empty($request->title) || empty($request->content) || empty($request->category))) {
                $validator->errors()->add('validation', 'When no image is uploaded, Title, Content, and Category are required.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');
        $data['created_by'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $data['image_path'] = $path;

            // Clear text overlay fields when image is uploaded to avoid redundancy
            // But don't include them in the data array to avoid null constraint violations
            unset($data['title'], $data['content'], $data['category']);
        }

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        // Custom validation logic based on whether image is uploaded or exists
        $rules = [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'background_color' => 'nullable|string|max:10',
            'priority' => 'nullable|integer|min:0|max:10',
        ];

        // Check if there will be an image after this update
        $willHaveImage = $request->hasFile('image') || (!$request->has('remove_image') && $announcement->image_path);

        // If no image will exist after update, require title, content, and category
        if (!$willHaveImage) {
            $rules['title'] = 'required|string|max:255';
            $rules['content'] = 'required|string';
            $rules['category'] = 'required|string|in:announcement,recognition,important_update,upcoming_event';
        } else {
            // If image will exist, these fields are optional
            $rules['title'] = 'nullable|string|max:255';
            $rules['content'] = 'nullable|string';
            $rules['category'] = 'nullable|string|in:announcement,recognition,important_update,upcoming_event';
        }

        $validator = Validator::make($request->all(), $rules);

        // Add custom validation message
        $validator->after(function ($validator) use ($request, $announcement) {
            $willHaveImage = $request->hasFile('image') || (!$request->has('remove_image') && $announcement->image_path);
            if (!$willHaveImage && (empty($request->title) || empty($request->content) || empty($request->category))) {
                $validator->errors()->add('validation', 'When no image is present, Title, Content, and Category are required.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');
        $data['updated_by'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            $path = $request->file('image')->store('announcements', 'public');
            $data['image_path'] = $path;

            // Clear text overlay fields when image is uploaded to avoid redundancy
            // But don't include them in the data array to avoid null constraint violations
            unset($data['title'], $data['content'], $data['category']);
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement)
    {
        // Delete image if exists
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }
        
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
    
    /**
     * Toggle the active status of an announcement.
     */
    public function toggleStatus(Announcement $announcement)
    {
        $announcement->update([
            'is_active' => !$announcement->is_active,
            'updated_by' => Auth::id(),
        ]);
        
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement status updated successfully.');
    }
} 