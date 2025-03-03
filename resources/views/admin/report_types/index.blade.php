@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Report Types Management</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h3>Add New Report Type</h3>
    <form action="{{ route('admin.report_types.store') }}" method="POST">
        @csrf
        <label for="name">Report Type Name:</label>
        <input type="text" name="name" required>

        <label for="frequency">Frequency:</label>
        <select name="frequency">
            @foreach(\App\Models\ReportType::frequencies() as $freq)
                <option value="{{ $freq }}">{{ ucfirst($freq) }}</option>
            @endforeach
        </select>

        <button type="submit">Create Report Type</button>
    </form>

    <h3>Existing Report Types</h3>
    <ul>
        @foreach($reportTypes as $type)
            <li>{{ $type->formatted_name }} - {{ ucfirst($type->frequency) }}</li>
        @endforeach
    </ul>
</div>
@endsection
