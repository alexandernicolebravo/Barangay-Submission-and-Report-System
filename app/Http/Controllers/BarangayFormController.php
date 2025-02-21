<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportForm;

class BarangayFormController extends Controller
{
    public function index()
    {
        $forms = ReportForm::all();
        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ReportForm::create($request->all());

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully.');
    }
}


