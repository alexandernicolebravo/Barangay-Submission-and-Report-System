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
        // Get all barangay users
        $barangays = User::where(function($query) {
            $query->where('role', 'barangay')
                  ->orWhere('user_type', 'barangay');
        })->get();

        // Get all report types
        $reportTypes = ReportType::all();

        // Create a directory to store sample files if it doesn't exist
        if (!Storage::disk('public')->exists('reports')) {
            Storage::disk('public')->makeDirectory('reports');
        }

        // Find existing PDF files in the public directory
        $sampleFiles = $this->findExistingPdfFiles();

        // Start date for submissions (January 1st of current year)
        $startDate = Carbon::create(Carbon::now()->year, 1, 1, 0, 0, 0);

        // End date for submissions (current date)
        $endDate = Carbon::now();

        // Days between start and end date
        $daysBetween = $startDate->diffInDays($endDate);

        echo "Seeding report submissions...\n";

        // For each barangay
        foreach ($barangays as $barangay) {
            echo "Processing barangay: {$barangay->name}\n";

            // For each report type
            foreach ($reportTypes as $reportType) {
                // Randomly decide if this barangay will submit this report type
                // 80% chance of submission, 20% chance of no submission
                $willSubmit = (rand(1, 100) <= 80);

                if (!$willSubmit) {
                    echo "  - Skipped {$reportType->frequency} report: {$reportType->name}\n";
                    continue;
                }

                // Generate a random date between start and end date
                $randomDays = rand(0, $daysBetween);
                $submissionDate = $startDate->copy()->addDays($randomDays);

                // Determine if this will be a late submission (30% chance)
                $isLateSubmission = (rand(1, 100) <= 30);

                // Choose a random sample file
                $sampleFile = $sampleFiles[array_rand($sampleFiles)];

                // Copy the sample file to the storage directory with a unique name
                $fileName = time() . '_' . $barangay->id . '_' . $reportType->id . '_' . $sampleFile['name'];
                $filePath = 'reports/' . $fileName;

                // Copy the file to storage
                Storage::disk('public')->put(
                    $filePath,
                    File::get($sampleFile['path'])
                );

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
                    $commonData['file_name'] = $sampleFile['name'];
                }

                // Add deadline if the column exists
                if (in_array('deadline', $columns)) {
                    // For late submissions, set the deadline to be before the submission date
                    if ($isLateSubmission) {
                        // Set deadline to be 5-15 days before the submission date
                        $daysLate = rand(5, 15);
                        $commonData['deadline'] = $submissionDate->copy()->subDays($daysLate);
                        echo "  - Added LATE {$reportType->frequency} report: {$reportType->name} ({$daysLate} days late)\n";
                    } else {
                        // For on-time submissions, set the deadline to be after the submission date
                        $daysAhead = rand(10, 30);
                        $commonData['deadline'] = $submissionDate->copy()->addDays($daysAhead);
                        echo "  - Added on-time {$reportType->frequency} report: {$reportType->name}\n";
                    }
                }

                // Add frequency-specific fields if they exist in the table
                switch ($reportType->frequency) {
                    case 'weekly':
                        $additionalData = [];

                        if (in_array('month', $columns)) {
                            $additionalData['month'] = $submissionDate->format('F');
                        }

                        if (in_array('week_number', $columns)) {
                            $additionalData['week_number'] = rand(1, 4);
                        }

                        if (in_array('num_of_clean_up_sites', $columns)) {
                            $additionalData['num_of_clean_up_sites'] = rand(1, 10);
                        }

                        if (in_array('num_of_participants', $columns)) {
                            $additionalData['num_of_participants'] = rand(10, 100);
                        }

                        if (in_array('num_of_barangays', $columns)) {
                            $additionalData['num_of_barangays'] = rand(1, 5);
                        }

                        if (in_array('total_volume', $columns)) {
                            $additionalData['total_volume'] = rand(10, 1000) / 10;
                        }

                        $reportData = array_merge($commonData, $additionalData);
                        break;

                    case 'monthly':
                        $additionalData = [];

                        if (in_array('month', $columns)) {
                            $additionalData['month'] = $submissionDate->format('F');
                        }

                        $reportData = array_merge($commonData, $additionalData);
                        break;

                    case 'quarterly':
                        $additionalData = [];
                        $quarter = ceil($submissionDate->format('n') / 3);

                        if (in_array('quarter_number', $columns)) {
                            $additionalData['quarter_number'] = (string) $quarter;
                        }

                        if (in_array('year', $columns)) {
                            $additionalData['year'] = $submissionDate->format('Y');
                        }

                        $reportData = array_merge($commonData, $additionalData);
                        break;

                    case 'semestral':
                        $additionalData = [];
                        $semester = ceil($submissionDate->format('n') / 6);

                        if (in_array('sem_number', $columns)) {
                            $additionalData['sem_number'] = (string) $semester;
                        }

                        $reportData = array_merge($commonData, $additionalData);
                        break;

                    case 'annual':
                        $reportData = $commonData;
                        break;

                    default:
                        $reportData = $commonData;
                }

                // Insert the report submission record
                DB::table($tableName)->insert($reportData);
            }
        }

        echo "Report submissions seeding completed!\n";
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

        // First, check if we have a sample_files directory in public
        if (File::exists(public_path('sample_files'))) {
            // Get all PDF files from the sample_files directory
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

        // If we don't have any sample files, create some minimal ones
        if (empty($sampleFiles)) {
            // Create sample_files directory if it doesn't exist
            if (!File::exists(public_path('sample_files'))) {
                File::makeDirectory(public_path('sample_files'));
            }

            // Create 5 minimal PDF files
            for ($i = 1; $i <= 5; $i++) {
                $fileName = "sample{$i}.pdf";
                $filePath = public_path('sample_files/' . $fileName);

                // Create a minimal PDF file
                $pdfContent = "%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Resources<<>>/Parent 2 0 R>>endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000053 00000 n
0000000102 00000 n
trailer<</Size 4/Root 1 0 R>>
startxref
178
%%EOF";

                // Write the PDF content to the file
                File::put($filePath, $pdfContent);

                $sampleFiles[] = [
                    'path' => $filePath,
                    'name' => $fileName
                ];
            }
        }

        return $sampleFiles;
    }
}
