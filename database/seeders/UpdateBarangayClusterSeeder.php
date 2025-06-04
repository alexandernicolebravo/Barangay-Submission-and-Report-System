<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cluster;

class UpdateBarangayClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure clusters exist
        $clusters = [
            ['id' => 1, 'name' => 'Cluster 1', 'description' => 'John\'s Cluster'],
            ['id' => 2, 'name' => 'Cluster 2', 'description' => 'Emelyn\'s Cluster'],
            ['id' => 3, 'name' => 'Cluster 3', 'description' => 'Greg\'s Cluster'],
            ['id' => 4, 'name' => 'Cluster 4', 'description' => 'Sandra\'s Cluster'],
        ];

        foreach ($clusters as $cluster) {
            Cluster::updateOrCreate(
                ['id' => $cluster['id']],
                $cluster + ['is_active' => true]
            );
        }

        // Define barangay assignments according to your specifications
        $barangayAssignments = [
            // John (Cluster 1)
            1 => [
                'Barangay 9', 'Barangay 10', 'Barangay 11', 'Barangay 12', 'Barangay 13', 
                'Barangay 14', 'Barangay 15', 'Barangay 16', 'Barangay 21', 'Barangay 22', 
                'Barangay 24', 'Barangay 25', 'Barangay 30', 'Barangay 41', 'Mansilingan', 'Montevista'
            ],
            // Emelyn (Cluster 2)
            2 => [
                'Barangay 20', 'Barangay 28', 'Barangay 29', 'Barangay 32', 'Barangay 34', 
                'Barangay 35', 'Barangay 36', 'Barangay 37', 'Barangay 38', 'Barangay 39', 
                'Bata', 'Banago', 'Mandalagan', 'Vista', 'Taculing', 'Estefania'
            ],
            // Greg (Cluster 3)
            3 => [
                'Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 5', 
                'Barangay 6', 'Barangay 7', 'Barangay 8', 'Barangay 17', 'Barangay 18', 
                'Barangay 23', 'Barangay 26', 'Barangay 31', 'Barangay 40', 'Granada'
            ],
            // Sandra (Cluster 4)
            4 => [
                'Barangay 19', 'Barangay 27', 'Barangay 33', 'Pahanocoy', 'Singcang',
                'Tangub', 'Punta Taytay', 'Cabug', 'Sum-ag', 'Felisa',
                'Handumanan', 'Alijis', 'Alangilan', 'Villamonte'
            ]
        ];

        // Clear existing barangay users
        User::where('user_type', 'barangay')->delete();

        // Create barangay users according to the new assignments
        foreach ($barangayAssignments as $clusterId => $barangays) {
            foreach ($barangays as $barangayName) {
                // Generate email based on barangay name
                $email = $this->generateEmail($barangayName);
                
                User::create([
                    'name' => $barangayName,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'user_type' => 'barangay',
                    'cluster_id' => $clusterId,
                    'is_active' => true,
                ]);
            }
        }

        // Fix facilitator-cluster assignments
        $facilitatorAssignments = [
            ['email' => 'john.cluster1@gmail.com', 'cluster_id' => 1],
            ['email' => 'emelyn.cluster2@gmail.com', 'cluster_id' => 2],
            ['email' => 'greg.cluster3@gmail.com', 'cluster_id' => 3],
            ['email' => 'sandra.cluster4@gmail.com', 'cluster_id' => 4],
        ];

        foreach ($facilitatorAssignments as $assignment) {
            $facilitator = User::where('email', $assignment['email'])
                              ->where('user_type', 'facilitator')
                              ->first();

            if ($facilitator) {
                // Remove any existing assignments for this facilitator
                DB::table('facilitator_cluster')->where('user_id', $facilitator->id)->delete();

                // Add the correct assignment
                DB::table('facilitator_cluster')->insert([
                    'user_id' => $facilitator->id,
                    'cluster_id' => $assignment['cluster_id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $this->command->info("Assigned {$facilitator->name} to Cluster {$assignment['cluster_id']}");
            } else {
                $this->command->warn("Facilitator with email {$assignment['email']} not found");
            }
        }

        $this->command->info('Barangay cluster assignments updated successfully!');
        $this->command->info('Total barangays created: ' . User::where('user_type', 'barangay')->count());

        // Show summary by cluster
        foreach ($barangayAssignments as $clusterId => $barangays) {
            $clusterName = Cluster::find($clusterId)->name;
            $this->command->info("$clusterName: " . count($barangays) . " barangays");
        }
    }

    /**
     * Generate email based on barangay name
     */
    private function generateEmail($barangayName): string
    {
        // Convert barangay name to email format
        $emailName = strtolower(str_replace(' ', '', $barangayName));
        
        // Handle special cases for numbered barangays
        if (preg_match('/^barangay\s*(\d+)$/i', $barangayName, $matches)) {
            return "barangay{$matches[1]}@gmail.com";
        }
        
        // For named barangays, use the name directly
        return "barangay{$emailName}@gmail.com";
    }
}
