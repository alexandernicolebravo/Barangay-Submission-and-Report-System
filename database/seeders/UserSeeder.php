<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('adminpassword'),
        //     'role' => 'admin',
        // ]);
        // User::create([
        //     'name' => 'John (Cluster 1)',
        //     'email' => 'john.cluster1@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'facilitator',
        //     'cluster_id' => null,
        //     'is_active' => true,
        // ]);

        // User::create([
        //     'name' => 'Emelyn (Cluster 2)',
        //     'email' => 'emelyn.cluster2@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'facilitator',
        //     'cluster_id' => null,
        //     'is_active' => true,
        // ]);


        // User::create([
        //     'name' => 'Greg (Cluster 3)',
        //     'email' => 'greg.cluster3@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'facilitator',
        //     'cluster_id' => null,
        //     'is_active' => true,
        // ]);

        // User::create([
        //     'name' => 'Sandra (Cluster 4)',
        //     'email' => 'sandra.cluster4@gmail.com',
        //     'password' => Hash::make('password'),
        //     'role' => 'facilitator',
        //     'cluster_id' => null,
        //     'is_active' => true,
        // ]);

        // // List of barangays
        // $barangayNames = [
        //     '19', '27', '33', 'Pahanocoy', 'Singcang','Tangub',
        //     'Pta. Taytay', 'Cabug', 'Sum-ag', 'Felisa', 'Handumanan', 'Alijis', 'Alangilan'
        //     , 'Villamonte'
        // ];

        // // Create barangays under this cluster
        // foreach ($barangayNames as $barangay) {
        //     User::create([
        //         'name' => "Barangay $barangay",
        //         'email' => strtolower("barangay$barangay@gmail.com"),
        //         'password' => Hash::make('password'),
        //         'role' => 'barangay',
        //         'cluster_id' => $cluster->id, // Assign to Cluster 1
        //         'is_active' => true,
        //     ]);
        // }



// Assuming you have a way to identify "Cluster 4" (e.g., by name or a unique identifier)

// Find Cluster
// $cluster1 = User::where('name', 'John (Cluster 1)')->first();
// $cluster2 = User::where('name', 'Emelyn (Cluster 2)')->first();

$cluster3 = User::where('name', 'Greg (Cluster 3)')->first();
$cluster4 = User::where('name', 'Sandra (Cluster 4)')->first();



// if ($cluster1) { // Check if Cluster 1 was found
//     $barangayNames = [
//         '9', '10', '11', '12', '13', '14',
//         '15', '16', '21', '22', '24', '25', '30',
//         '41','Mansilingan', 'Montevista'
//     ];

//     // Create barangays under Cluster 1
//     foreach ($barangayNames as $barangay) {
//         User::create([
//             'name' => "Barangay $barangay",
//             'email' => strtolower("barangay$barangay@gmail.com"),
//             'password' => Hash::make('password'),
//             'role' => 'barangay',
//             'cluster_id' => $cluster1->id, // Assign to Cluster 1
//             'is_active' => true,
//         ]);
//     }
// }

// if ($cluster2) { // Check if Cluster 4 was found
//     $barangayNames = [
//         '20', '28', '29', '32', '34', '35',
//         '36', '37', '38', '39', 'Bata', 'Banago', 'Mandalagan',
//         'Vista','Taculing', 'Estefania'
//     ];

//     // Create barangays under Cluster 4
//     foreach ($barangayNames as $barangay) {
//         User::create([
//             'name' => "Barangay $barangay",
//             'email' => strtolower("barangay$barangay@gmail.com"),
//             'password' => Hash::make('password'),
//             'role' => 'barangay',
//             'cluster_id' => $cluster2->id, // Assign to Cluster 2
//             'is_active' => true,
//         ]);
//     }
// }

if ($cluster3) { // Check if Cluster 4 was found
    $barangayNames = [
        '1', '2', '3', '4', '5', '6',
        '7', '8', '17', '18', '23', '26', '31',
        '40','Granada'
    ];

    // Create barangays under Cluster 4
    foreach ($barangayNames as $barangay) {
        User::create([
            'name' => "Barangay $barangay",
            'email' => strtolower("barangay$barangay@gmail.com"),
            'password' => Hash::make('password'),
            'role' => 'barangay',
            'cluster_id' => $cluster3->id, // Assign to Cluster 2
            'is_active' => true,
        ]);
    }
}

if ($cluster4) { // Check if Cluster 4 was found
    $barangayNames = [
        '19', '27', '33', 'Pahanocoy', 'Singcang','Tangub',
            'Pta. Taytay', 'Cabug', 'Sum-ag', 'Felisa', 'Handumanan', 'Alijis', 'Alangilan'
            , 'Villamonte'
    ];

    // Create barangays under Cluster 4
    foreach ($barangayNames as $barangay) {
        User::create([
            'name' => "Barangay $barangay",
            'email' => strtolower("barangay$barangay@gmail.com"),
            'password' => Hash::make('password'),
            'role' => 'barangay',
            'cluster_id' => $cluster4->id, // Assign to Cluster 2
            'is_active' => true,
        ]);
    }
}
    }
}
