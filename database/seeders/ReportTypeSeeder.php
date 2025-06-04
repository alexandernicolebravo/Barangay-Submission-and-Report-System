<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ReportType;
use Carbon\Carbon;

class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing report types (use delete instead of truncate to avoid foreign key issues)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ReportType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $reportTypes = $this->getReportTypesData();

        foreach ($reportTypes as $reportType) {
            ReportType::create($reportType);
        }
    }

    /**
     * Get comprehensive report types data
     */
    private function getReportTypesData()
    {
        $reportTypes = [];

        // Monthly Reports with specific months (as requested)
        $monthlyReports = [
            ['name' => 'BADAC', 'month' => 'January'],
            ['name' => 'ROAD CLEARING', 'month' => 'February'],
            ['name' => 'FIRST TIME JOB SEEKERS ASSISTANCE', 'month' => 'March'],
            ['name' => 'KASAMBAHAY REPORTS', 'month' => 'April'],
            ['name' => 'LUPON MINUTES', 'month' => 'May'],
        ];

        foreach ($monthlyReports as $index => $report) {
            // Set deadlines within 2024-2025 range
            $year = rand(0, 1) == 0 ? 2024 : 2025;
            $month = rand(1, 12);
            $deadline = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();

            // Make some deadlines in the past (2024) and some in future (2025)
            if ($index % 2 == 0) {
                $deadline = Carbon::create(2024, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            } else {
                $deadline = Carbon::create(2025, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => 'monthly',
                'deadline' => $deadline->toDateString(),
                'instructions' => "Monthly {$report['name']} report for {$report['month']}. Please submit all required documentation and ensure compliance with local regulations.",
                'allowed_file_types' => ['pdf', 'docx', 'xlsx'],
                'file_naming_format' => strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{MONTH}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Quarterly Reports (as requested)
        $quarterlyReports = [
            ['name' => 'KP', 'quarter' => 'Q1', 'description' => 'Katarungang Pambarangay Report for First Quarter'],
            ['name' => 'VAW/VAC', 'quarter' => 'Q2', 'description' => 'Violence Against Women and Children Report for Second Quarter'],
            ['name' => 'BFDP', 'quarter' => 'Q3', 'description' => 'Barangay Financial Development Plan for Third Quarter'],
        ];

        foreach ($quarterlyReports as $index => $report) {
            // Set quarterly deadlines within 2024-2025 range
            $year = rand(0, 1) == 0 ? 2024 : 2025;
            $quarter = rand(1, 4);
            $quarterEndMonth = $quarter * 3;
            $deadline = Carbon::create($year, $quarterEndMonth, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();

            // Make some deadlines in the past (2024) and some in future (2025)
            if ($index % 2 == 0) {
                $deadline = Carbon::create(2024, rand(1, 4) * 3, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            } else {
                $deadline = Carbon::create(2025, rand(1, 4) * 3, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => 'quarterly',
                'deadline' => $deadline->toDateString(),
                'instructions' => $report['description'] . '. Submit comprehensive quarterly analysis and recommendations.',
                'allowed_file_types' => ['pdf', 'docx', 'xlsx'],
                'file_naming_format' => $report['name'] . '_{BARANGAY}_{QUARTER}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Additional Monthly Reports
        $additionalMonthlyReports = [
            ['name' => 'BARANGAY HEALTH REPORT', 'month' => 'June'],
            ['name' => 'ENVIRONMENTAL COMPLIANCE REPORT', 'month' => 'July'],
            ['name' => 'DISASTER PREPAREDNESS REPORT', 'month' => 'August'],
            ['name' => 'YOUTH DEVELOPMENT REPORT', 'month' => 'September'],
            ['name' => 'SENIOR CITIZEN WELFARE REPORT', 'month' => 'October'],
            ['name' => 'WOMEN EMPOWERMENT REPORT', 'month' => 'November'],
            ['name' => 'PEACE AND ORDER REPORT', 'month' => 'December'],
            ['name' => 'INFRASTRUCTURE DEVELOPMENT REPORT', 'month' => 'January'],
            ['name' => 'EDUCATION SUPPORT REPORT', 'month' => 'February'],
            ['name' => 'LIVELIHOOD PROGRAM REPORT', 'month' => 'March'],
            ['name' => 'SANITATION PROGRAM REPORT', 'month' => 'April'],
            ['name' => 'NUTRITION PROGRAM REPORT', 'month' => 'May'],
            ['name' => 'SPORTS AND RECREATION REPORT', 'month' => 'June'],
            ['name' => 'CULTURAL ACTIVITIES REPORT', 'month' => 'July'],
            ['name' => 'COMMUNITY GARDEN REPORT', 'month' => 'August'],
            ['name' => 'WASTE MANAGEMENT REPORT', 'month' => 'September'],
            ['name' => 'WATER SYSTEM REPORT', 'month' => 'October'],
            ['name' => 'ELECTRICITY SERVICES REPORT', 'month' => 'November'],
            ['name' => 'TRANSPORTATION REPORT', 'month' => 'December'],
            ['name' => 'BUSINESS PERMIT REPORT', 'month' => 'January'],
        ];

        foreach ($additionalMonthlyReports as $index => $report) {
            // Set deadlines within 2024-2025 range
            if ($index % 3 == 0) {
                // Past deadlines in 2024
                $deadline = Carbon::create(2024, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            } else {
                // Future deadlines in 2025
                $deadline = Carbon::create(2025, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => 'monthly',
                'deadline' => $deadline->toDateString(),
                'instructions' => "Monthly {$report['name']} for {$report['month']}. Include all relevant data, statistics, and recommendations for improvement.",
                'allowed_file_types' => ['pdf', 'docx', 'xlsx'],
                'file_naming_format' => strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{MONTH}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Weekly Reports
        $weeklyReports = [
            'KALINISAN WEEKLY REPORT',
            'SECURITY PATROL REPORT',
            'MARKET INSPECTION REPORT',
            'TRAFFIC MANAGEMENT REPORT',
            'HEALTH CENTER OPERATIONS REPORT',
            'BARANGAY HALL ACTIVITIES REPORT',
            'COMMUNITY OUTREACH REPORT',
            'EMERGENCY RESPONSE REPORT',
            'MAINTENANCE AND REPAIRS REPORT',
            'VISITOR LOG REPORT',
            'INCIDENT REPORT',
            'EQUIPMENT INVENTORY REPORT',
            'UTILITY CONSUMPTION REPORT',
            'STAFF ATTENDANCE REPORT',
            'COMPLAINT RESOLUTION REPORT',
        ];

        foreach ($weeklyReports as $index => $reportName) {
            // Set weekly deadlines within 2024-2025 range
            if ($index % 4 == 0) {
                // Past deadlines in 2024
                $weekNumber = rand(1, 52);
                $deadline = Carbon::create(2024, 1, 1, 0, 0, 0, 'Asia/Manila')->addWeeks($weekNumber)->endOfWeek();
            } else {
                // Future deadlines in 2025
                $weekNumber = rand(1, 52);
                $deadline = Carbon::create(2025, 1, 1, 0, 0, 0, 'Asia/Manila')->addWeeks($weekNumber)->endOfWeek();
            }

            $reportTypes[] = [
                'name' => $reportName,
                'frequency' => 'weekly',
                'deadline' => $deadline->toDateString(),
                'instructions' => "Weekly {$reportName}. Submit every Friday before 5:00 PM. Include detailed activities, issues encountered, and recommendations.",
                'allowed_file_types' => ['pdf', 'docx'],
                'file_naming_format' => strtoupper(str_replace(' ', '_', $reportName)) . '_{BARANGAY}_WEEK_{WEEK_NUMBER}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Additional Quarterly Reports
        $additionalQuarterlyReports = [
            ['name' => 'BUDGET UTILIZATION REPORT', 'quarter' => 'Q4', 'description' => 'Quarterly Budget Utilization and Financial Performance Report for Fourth Quarter'],
            ['name' => 'COMMUNITY DEVELOPMENT REPORT', 'quarter' => 'Q1', 'description' => 'Community Development Progress Report for First Quarter'],
            ['name' => 'HEALTH SERVICES REPORT', 'quarter' => 'Q2', 'description' => 'Barangay Health Services and Medical Assistance Report for Second Quarter'],
            ['name' => 'EDUCATION ASSISTANCE REPORT', 'quarter' => 'Q3', 'description' => 'Educational Support and Scholarship Program Report for Third Quarter'],
            ['name' => 'INFRASTRUCTURE ASSESSMENT REPORT', 'quarter' => 'Q4', 'description' => 'Infrastructure Development and Maintenance Assessment for Fourth Quarter'],
            ['name' => 'ENVIRONMENTAL PROTECTION REPORT', 'quarter' => 'Q1', 'description' => 'Environmental Protection and Conservation Activities Report for First Quarter'],
            ['name' => 'SOCIAL SERVICES REPORT', 'quarter' => 'Q2', 'description' => 'Social Services and Welfare Programs Report for Second Quarter'],
            ['name' => 'ECONOMIC DEVELOPMENT REPORT', 'quarter' => 'Q3', 'description' => 'Economic Development and Livelihood Programs Report for Third Quarter'],
            ['name' => 'GOVERNANCE REPORT', 'quarter' => 'Q4', 'description' => 'Good Governance and Transparency Report for Fourth Quarter'],
            ['name' => 'DISASTER RISK REDUCTION REPORT', 'quarter' => 'Q1', 'description' => 'Disaster Risk Reduction and Management Report for First Quarter'],
        ];

        foreach ($additionalQuarterlyReports as $index => $report) {
            // Set quarterly deadlines within 2024-2025 range
            if ($index % 2 == 0) {
                // Past deadlines in 2024
                $quarter = rand(1, 4);
                $deadline = Carbon::create(2024, $quarter * 3, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            } else {
                // Future deadlines in 2025
                $quarter = rand(1, 4);
                $deadline = Carbon::create(2025, $quarter * 3, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => 'quarterly',
                'deadline' => $deadline->toDateString(),
                'instructions' => $report['description'] . '. Provide comprehensive analysis, achievements, challenges, and strategic recommendations.',
                'allowed_file_types' => ['pdf', 'docx', 'xlsx'],
                'file_naming_format' => strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{QUARTER}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Semestral Reports
        $semestralReports = [
            ['name' => 'COMPREHENSIVE BARANGAY PERFORMANCE REPORT', 'semester' => '1st Semester', 'description' => 'Comprehensive Performance and Achievement Report for First Semester'],
            ['name' => 'FINANCIAL ACCOUNTABILITY REPORT', 'semester' => '2nd Semester', 'description' => 'Financial Accountability and Transparency Report for Second Semester'],
            ['name' => 'COMMUNITY SATISFACTION SURVEY REPORT', 'semester' => '1st Semester', 'description' => 'Community Satisfaction and Feedback Survey Report for First Semester'],
            ['name' => 'STRATEGIC PLANNING REPORT', 'semester' => '2nd Semester', 'description' => 'Strategic Planning and Development Report for Second Semester'],
            ['name' => 'HUMAN RESOURCE DEVELOPMENT REPORT', 'semester' => '1st Semester', 'description' => 'Human Resource Development and Training Report for First Semester'],
            ['name' => 'TECHNOLOGY INTEGRATION REPORT', 'semester' => '2nd Semester', 'description' => 'Technology Integration and Digital Services Report for Second Semester'],
            ['name' => 'PARTNERSHIP AND COLLABORATION REPORT', 'semester' => '1st Semester', 'description' => 'Partnership and Collaboration Activities Report for First Semester'],
            ['name' => 'INNOVATION AND BEST PRACTICES REPORT', 'semester' => '2nd Semester', 'description' => 'Innovation and Best Practices Implementation Report for Second Semester'],
        ];

        foreach ($semestralReports as $index => $report) {
            // Set semestral deadlines within 2024-2025 range
            if ($index % 2 == 0) {
                // Past deadlines in 2024 (June and December)
                $semesterMonth = rand(0, 1) == 0 ? 6 : 12;
                $deadline = Carbon::create(2024, $semesterMonth, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            } else {
                // Future deadlines in 2025 (June and December)
                $semesterMonth = rand(0, 1) == 0 ? 6 : 12;
                $deadline = Carbon::create(2025, $semesterMonth, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => 'semestral',
                'deadline' => $deadline->toDateString(),
                'instructions' => $report['description'] . '. Submit detailed semestral analysis with supporting documents, statistical data, and future plans.',
                'allowed_file_types' => ['pdf', 'docx', 'xlsx', 'zip'],
                'file_naming_format' => strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{SEMESTER}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Annual Reports
        $annualReports = [
            ['name' => 'ANNUAL ACCOMPLISHMENT REPORT', 'description' => 'Comprehensive Annual Accomplishment and Performance Report'],
            ['name' => 'ANNUAL FINANCIAL REPORT', 'description' => 'Annual Financial Statement and Budget Utilization Report'],
            ['name' => 'ANNUAL DEVELOPMENT PLAN REPORT', 'description' => 'Annual Development Plan Implementation and Progress Report'],
            ['name' => 'ANNUAL AUDIT REPORT', 'description' => 'Annual Internal Audit and Compliance Report'],
            ['name' => 'ANNUAL COMMUNITY IMPACT REPORT', 'description' => 'Annual Community Impact Assessment and Evaluation Report'],
            ['name' => 'ANNUAL SUSTAINABILITY REPORT', 'description' => 'Annual Environmental and Social Sustainability Report'],
            ['name' => 'ANNUAL GOVERNANCE REPORT', 'description' => 'Annual Good Governance and Transparency Report'],
            ['name' => 'ANNUAL STATISTICAL REPORT', 'description' => 'Annual Statistical Data and Demographics Report'],
            ['name' => 'ANNUAL RISK ASSESSMENT REPORT', 'description' => 'Annual Risk Assessment and Management Report'],
            ['name' => 'ANNUAL STAKEHOLDER REPORT', 'description' => 'Annual Stakeholder Engagement and Communication Report'],
        ];

        foreach ($annualReports as $index => $report) {
            // Set annual deadlines within 2024-2025 range
            if ($index % 3 == 0) {
                // Past deadlines in 2024
                $deadline = Carbon::create(2024, 12, 31, 0, 0, 0, 'Asia/Manila');
            } else {
                // Future deadlines in 2025
                $deadline = Carbon::create(2025, 12, 31, 0, 0, 0, 'Asia/Manila');
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => 'annual',
                'deadline' => $deadline->toDateString(),
                'instructions' => $report['description'] . '. Submit comprehensive annual report with complete documentation, analysis, and recommendations for the following year.',
                'allowed_file_types' => ['pdf', 'docx', 'xlsx', 'zip'],
                'file_naming_format' => strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{YEAR}',
                'archived_at' => null,
            ];
        }

        // Additional Special Reports to reach 80+
        $specialReports = [
            ['name' => 'COVID-19 RESPONSE REPORT', 'frequency' => 'monthly', 'month' => 'February'],
            ['name' => 'ANTI-DRUG CAMPAIGN REPORT', 'frequency' => 'quarterly', 'quarter' => 'Q2'],
            ['name' => 'SENIOR CITIZEN ID ISSUANCE REPORT', 'frequency' => 'monthly', 'month' => 'March'],
            ['name' => 'PWD ASSISTANCE REPORT', 'frequency' => 'monthly', 'month' => 'April'],
            ['name' => 'SOLO PARENT SUPPORT REPORT', 'frequency' => 'quarterly', 'quarter' => 'Q3'],
            ['name' => 'BARANGAY CLEARANCE ISSUANCE REPORT', 'frequency' => 'weekly'],
            ['name' => 'BUSINESS CLOSURE REPORT', 'frequency' => 'monthly', 'month' => 'May'],
            ['name' => 'FIRE SAFETY INSPECTION REPORT', 'frequency' => 'quarterly', 'quarter' => 'Q4'],
            ['name' => 'FLOOD CONTROL MAINTENANCE REPORT', 'frequency' => 'semestral', 'semester' => '1st Semester'],
            ['name' => 'STREET LIGHTING REPORT', 'frequency' => 'monthly', 'month' => 'June'],
            ['name' => 'PUBLIC MARKET OPERATIONS REPORT', 'frequency' => 'weekly'],
            ['name' => 'CEMETERY MAINTENANCE REPORT', 'frequency' => 'quarterly', 'quarter' => 'Q1'],
            ['name' => 'BARANGAY VEHICLE MAINTENANCE REPORT', 'frequency' => 'monthly', 'month' => 'July'],
            ['name' => 'COMMUNICATION EQUIPMENT REPORT', 'frequency' => 'semestral', 'semester' => '2nd Semester'],
            ['name' => 'BOUNDARY DISPUTE RESOLUTION REPORT', 'frequency' => 'quarterly', 'quarter' => 'Q2'],
        ];

        foreach ($specialReports as $index => $report) {
            $frequency = $report['frequency'];

            switch ($frequency) {
                case 'weekly':
                    if ($index % 3 == 0) {
                        $weekNumber = rand(1, 52);
                        $deadline = Carbon::create(2024, 1, 1, 0, 0, 0, 'Asia/Manila')->addWeeks($weekNumber)->endOfWeek();
                    } else {
                        $weekNumber = rand(1, 52);
                        $deadline = Carbon::create(2025, 1, 1, 0, 0, 0, 'Asia/Manila')->addWeeks($weekNumber)->endOfWeek();
                    }
                    $instructions = "Weekly {$report['name']}. Submit every Friday with detailed weekly activities and observations.";
                    $fileFormat = strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_WEEK_{WEEK_NUMBER}_{YEAR}';
                    break;
                case 'monthly':
                    if ($index % 3 == 0) {
                        $deadline = Carbon::create(2024, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    } else {
                        $deadline = Carbon::create(2025, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    }
                    $instructions = "Monthly {$report['name']} for {$report['month']}. Include comprehensive monthly data and analysis.";
                    $fileFormat = strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{MONTH}_{YEAR}';
                    break;
                case 'quarterly':
                    if ($index % 3 == 0) {
                        $quarter = rand(1, 4);
                        $deadline = Carbon::create(2024, $quarter * 3, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    } else {
                        $quarter = rand(1, 4);
                        $deadline = Carbon::create(2025, $quarter * 3, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    }
                    $instructions = "Quarterly {$report['name']} for {$report['quarter']}. Provide detailed quarterly assessment and recommendations.";
                    $fileFormat = strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{QUARTER}_{YEAR}';
                    break;
                case 'semestral':
                    if ($index % 3 == 0) {
                        $semesterMonth = rand(0, 1) == 0 ? 6 : 12;
                        $deadline = Carbon::create(2024, $semesterMonth, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    } else {
                        $semesterMonth = rand(0, 1) == 0 ? 6 : 12;
                        $deadline = Carbon::create(2025, $semesterMonth, 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    }
                    $instructions = "Semestral {$report['name']} for {$report['semester']}. Submit comprehensive semestral evaluation.";
                    $fileFormat = strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{SEMESTER}_{YEAR}';
                    break;
                default:
                    if ($index % 3 == 0) {
                        $deadline = Carbon::create(2024, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    } else {
                        $deadline = Carbon::create(2025, rand(1, 12), 1, 0, 0, 0, 'Asia/Manila')->endOfMonth();
                    }
                    $instructions = "Special {$report['name']}. Submit as required with complete documentation.";
                    $fileFormat = strtoupper(str_replace(' ', '_', $report['name'])) . '_{BARANGAY}_{DATE}';
            }

            $reportTypes[] = [
                'name' => $report['name'],
                'frequency' => $frequency,
                'deadline' => $deadline->toDateString(),
                'instructions' => $instructions,
                'allowed_file_types' => ['pdf', 'docx', 'xlsx'],
                'file_naming_format' => $fileFormat,
                'archived_at' => null,
            ];
        }

        return $reportTypes;
    }
}
