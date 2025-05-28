<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetNonAdminPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-non-admin-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all non-admin user passwords to "password"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all non-admin users
        $users = User::where('user_type', '!=', 'admin')->get();

        $count = $users->count();

        if ($count === 0) {
            $this->info('No non-admin users found.');
            return;
        }

        $this->info("Found {$count} non-admin users. Resetting passwords...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($users as $user) {
            $user->update([
                'password' => Hash::make('password')
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All non-admin user passwords have been reset to "password".');
    }
}
