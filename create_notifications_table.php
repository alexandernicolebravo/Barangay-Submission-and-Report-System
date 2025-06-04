<?php

// Simple script to create notifications table
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Database configuration
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'dilg_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Check if table exists
    $tableExists = Capsule::schema()->hasTable('notifications');
    
    if ($tableExists) {
        echo "Notifications table already exists.\n";
    } else {
        echo "Creating notifications table...\n";
        
        Capsule::schema()->create('notifications', function ($table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        
        echo "Notifications table created successfully!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
