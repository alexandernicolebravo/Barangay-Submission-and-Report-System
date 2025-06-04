<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ReportType;

class ReportSubmissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "🚀 Starting comprehensive report submissions seeding...\n";

        // Clear existing submissions first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('weekly_reports')->truncate();
        DB::table('monthly_reports')->truncate();
        DB::table('quarterly_reports')->truncate();
        DB::table('semestral_reports')->truncate();
        DB::table('annual_reports')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get all barangay users
        $barangays = User::where('user_type', 'barangay')->get();
        echo "📍 Found {$barangays->count()} barangays\n";

        // Get all report types
        $reportTypes = ReportType::all();
        echo "📋 Found {$reportTypes->count()} report types\n";

        // Find existing PDF files
        $sampleFiles = $this->findExistingPdfFiles();
        echo "📁 Found " . count($sampleFiles) . " sample PDF files\n";

        // Submission date range: 2024-01-01 to today
        $startDate = Carbon::create(2024, 1, 1, 0, 0, 0, 'Asia/Manila');
        $endDate = Carbon::now('Asia/Manila');

        echo "📅 Submission date range: {$startDate->toDateString()} to {$endDate->toDateString()}\n";

        $totalSubmissions = 0;
        $submissionTracker = []; // Track submissions per barangay per report type

        // Generate 1000+ submissions randomly distributed across barangays and report types
        $targetSubmissions = 1200; // Target more than 1000 to ensure we get at least 1000
        $submissionsCreated = 0;

        echo "🎯 Target: {$targetSubmissions} submissions\n";

        while ($submissionsCreated < $targetSubmissions) {
            // Randomly select a barangay
            $barangay = $barangays->random();

            // Randomly select a report type
            $reportType = $reportTypes->random();

            // Create unique key to prevent duplicate submissions
            $key = $barangay->id . '_' . $reportType->id;
            if (isset($submissionTracker[$key])) {
                continue; // Skip if this combination already exists
            }
            $submissionTracker[$key] = true;

            // Generate random submission date (2024-01-01 to today)
            $daysBetween = $startDate->diffInDays($endDate);
            $randomDays = rand(0, $daysBetween);
            $submissionDate = $startDate->copy()->addDays($randomDays);

            // Ensure submission is not in the future
            if ($submissionDate->gt($endDate)) {
                $submissionDate = $endDate->copy()->subDays(rand(1, 30));
            }

            // Choose a random sample file
            $sampleFile = $sampleFiles[array_rand($sampleFiles)];

            // Create unique file name
            $timestamp = $submissionDate->timestamp;
            $fileName = $timestamp . '_' . $barangay->id . '_' . $reportType->id . '_' . $sampleFile['name'];
            $filePath = 'reports/' . $fileName;

            // Copy the file to storage
            try {
                Storage::disk('public')->put($filePath, File::get($sampleFile['path']));
            } catch (\Exception $e) {
                echo "⚠️  Error copying file: " . $e->getMessage() . "\n";
                continue;
            }

            // Create the submission
            $this->createReportSubmission($reportType, $barangay, $filePath, $fileName, $submissionDate);

            $submissionsCreated++;
            $totalSubmissions++;

            // Progress indicator
            if ($submissionsCreated % 100 == 0) {
                echo "✅ Created {$submissionsCreated} submissions...\n";
            }
        }

        echo "🎉 Report submissions seeding completed!\n";
        echo "📊 Total submissions created: {$totalSubmissions}\n";
    }

    /**
     * Create a report submission based on frequency
     */
    private function createReportSubmission($reportType, $barangay, $filePath, $fileName, $submissionDate)
    {
        // Determine which table to insert into based on report frequency
        $tableName = $this->getTableNameForFrequency($reportType->frequency);

        // Get the columns for this table
        $columns = $this->getTableColumns($tableName);

        // Prepare common data for all report types
        $commonData = [
            'user_id' => $barangay->id,
            'report_type_id' => $reportType->id,
            'file_path' => $filePath,
            'status' => 'submitted',
            'created_at' => $submissionDate,
            'updated_at' => $submissionDate,
        ];

        // Add file_name if the column exists
        if (in_array('file_name', $columns)) {
            $commonData['file_name'] = $fileName;
        }

        // Add deadline if the column exists (set to report type deadline)
        if (in_array('deadline', $columns)) {
            $commonData['deadline'] = $reportType->deadline;
        }

        // Add frequency-specific fields
        switch ($reportType->frequency) {
            case 'weekly':
                if (in_array('month', $columns)) {
                    $commonData['month'] = $submissionDate->format('F');
                }
                if (in_array('week_number', $columns)) {
                    $commonData['week_number'] = rand(1, 4);
                }
                if (in_array('num_of_clean_up_sites', $columns)) {
                    $commonData['num_of_clean_up_sites'] = rand(1, 10);
                }
                if (in_array('num_of_participants', $columns)) {
                    $commonData['num_of_participants'] = rand(10, 100);
                }
                if (in_array('num_of_barangays', $columns)) {
                    $commonData['num_of_barangays'] = rand(1, 5);
                }
                if (in_array('total_volume', $columns)) {
                    $commonData['total_volume'] = rand(10, 1000) / 10;
                }
                break;

            case 'monthly':
                if (in_array('month', $columns)) {
                    $commonData['month'] = $submissionDate->format('F');
                }
                break;

            case 'quarterly':
                $quarter = ceil($submissionDate->format('n') / 3);
                if (in_array('quarter_number', $columns)) {
                    $commonData['quarter_number'] = (string) $quarter;
                }
                if (in_array('year', $columns)) {
                    $commonData['year'] = $submissionDate->format('Y');
                }
                break;

            case 'semestral':
                $semester = ceil($submissionDate->format('n') / 6);
                if (in_array('sem_number', $columns)) {
                    $commonData['sem_number'] = (string) $semester;
                }
                break;
        }

        // Insert the report submission record
        DB::table($tableName)->insert($commonData);
    }

    /**
     * Get the table name for a given report frequency
     *
     * @param string $frequency
     * @return string
     */
    private function getTableNameForFrequency($frequency)
    {
        switch ($frequency) {
            case 'weekly':
                return 'weekly_reports';
            case 'monthly':
                return 'monthly_reports';
            case 'quarterly':
                return 'quarterly_reports';
            case 'semestral':
                return 'semestral_reports';
            case 'annual':
                return 'annual_reports';
            default:
                return 'monthly_reports'; // Default to monthly if unknown
        }
    }

    /**
     * Get the columns for a given table
     *
     * @param string $tableName
     * @return array
     */
    private function getTableColumns($tableName)
    {
        $columns = [];

        try {
            // Get the column information from the database
            $columnInfo = DB::select("SHOW COLUMNS FROM {$tableName}");

            // Extract just the column names
            foreach ($columnInfo as $column) {
                $columns[] = $column->Field;
            }
        } catch (\Exception $e) {
            echo "Error getting columns for table {$tableName}: " . $e->getMessage() . "\n";
        }

        return $columns;
    }

    /**
     * Find existing PDF files in the system
     *
     * @return array
     */
    private function findExistingPdfFiles()
    {
        $sampleFiles = [];

        // Priority 1: Check for existing real PDF files in storage/app/public/reports
        $storageReportsPath = storage_path('app/public/reports');
        if (File::exists($storageReportsPath)) {
            $files = File::files($storageReportsPath);
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) === 'pdf') {
                    // Only use files that are actual PDFs (larger than 1KB)
                    if ($file->getSize() > 1024) {
                        $sampleFiles[] = [
                            'path' => $file->getPathname(),
                            'name' => $file->getFilename()
                        ];
                    }
                }
            }
        }

        // Priority 2: Check public/sample_files directory
        if (empty($sampleFiles) && File::exists(public_path('sample_files'))) {
            $files = File::files(public_path('sample_files'));
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) === 'pdf') {
                    $sampleFiles[] = [
                        'path' => $file->getPathname(),
                        'name' => $file->getFilename()
                    ];
                }
            }
        }

        // Priority 3: Look for specific known good files
        $knownGoodFiles = [
            storage_path('app/public/reports/CORTEZANO-OJT REPORT.pdf'),
            storage_path('app/public/reports/1747730341_CORTEZANO-OJT REPORT.pdf'),
            storage_path('app/public/reports/1747368319_FrontisPiece_ServiceConnect1312312.pdf'),
            storage_path('app/public/reports/CHAPTER_1-Final.pdf'),
            storage_path('app/public/reports/1741137492_CHAPTER_1-Final.pdf'),
            public_path('sample_files/sample1.pdf'),
            public_path('sample_files/sample2.pdf'),
            public_path('sample_files/sample3.pdf'),
            public_path('sample_files/sample4.pdf'),
            public_path('sample_files/sample5.pdf'),
        ];

        foreach ($knownGoodFiles as $filePath) {
            if (File::exists($filePath) && File::size($filePath) > 1024) {
                $sampleFiles[] = [
                    'path' => $filePath,
                    'name' => basename($filePath)
                ];
            }
        }

        // Remove duplicates based on file name
        $uniqueFiles = [];
        $usedNames = [];
        foreach ($sampleFiles as $file) {
            if (!in_array($file['name'], $usedNames)) {
                $uniqueFiles[] = $file;
                $usedNames[] = $file['name'];
            }
        }

        echo "📁 Found " . count($uniqueFiles) . " real PDF files to use\n";
        foreach ($uniqueFiles as $file) {
            $size = File::size($file['path']);
            echo "  - {$file['name']} (" . number_format($size) . " bytes)\n";
        }

        return $uniqueFiles;
    }
}
