<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING REPORT TYPES ===\n";

// Get all active report types
$activeReports = \App\Models\ReportType::active()->get();
echo "Current active report types: " . $activeReports->count() . "\n\n";

// Group by name to find duplicates
$groupedByName = $activeReports->groupBy('name');

echo "=== REPORT TYPES BY NAME ===\n";
foreach ($groupedByName as $name => $reports) {
    echo "'{$name}': {$reports->count()} reports\n";
    if ($reports->count() > 1) {
        echo "  -> This has duplicates!\n";
        foreach ($reports as $report) {
            echo "     ID: {$report->id} | Deadline: {$report->deadline} | Frequency: {$report->frequency}\n";
        }
    }
}

echo "\n=== SUGGESTED ACTION ===\n";
echo "If you want to keep only 4 specific report types active, you should:\n";
echo "1. Go to Admin -> Create Report page\n";
echo "2. Archive all the unwanted report types (especially duplicate 'Kalinisan' reports)\n";
echo "3. Keep only the 4 report types you want active\n\n";

echo "=== CURRENT CALCULATION ===\n";
$barangayCount = \App\Models\User::where('user_type', 'barangay')->where('is_active', true)->count();
$activeCount = \App\Models\ReportType::active()->count();
$expectedTotal = $activeCount * $barangayCount;
echo "Active barangays: {$barangayCount}\n";
echo "Active report types: {$activeCount}\n";
echo "Expected total pending submissions: {$activeCount} × {$barangayCount} = {$expectedTotal}\n";
echo "Per barangay pending submissions: {$activeCount}\n\n";

echo "If you want each barangay to show 4 pending submissions, you need exactly 4 active report types.\n";

?>
