<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ReportSubmissionsSeeder;

class SeedReportSubmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:report-submissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed report submissions for all barangays and report types';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to seed report submissions...');
        
        // Run the seeder
        $seeder = new ReportSubmissionsSeeder();
        $seeder->run();
        
        $this->info('Report submissions seeding completed!');
        
        return 0;
    }
}
