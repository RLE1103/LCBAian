<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all alumni users
        $alumni = User::where('role', 'alumni')->get();
        
        // Define cluster groups based on common patterns
        $clusterGroups = [
            0 => ['program' => 'BSCS', 'industry' => 'technology'],
            1 => ['program' => 'BSIT', 'industry' => 'technology'],
            2 => ['program' => 'cese', 'industry' => 'engineering'],
            3 => ['skills' => ['python', 'machine learning', 'data science']],
            4 => ['industry' => 'finance', 'skills' => ['accounting', 'finance']]
        ];
        
        // Assign cluster groups to users based on their attributes
        foreach ($alumni as $user) {
            $cluster = null;
            
            // Determine cluster based on user attributes
            if ($user->program === 'BSCS' && strtolower($user->industry ?? '') === 'technology') {
                $cluster = 0;
            } elseif ($user->program === 'BSIT' && strtolower($user->industry ?? '') === 'technology') {
                $cluster = 1;
            } elseif ($user->program === 'cese') {
                $cluster = 2;
            } elseif ($user->skills) {
                $hasDataScience = array_intersect(['python', 'machine learning', 'data science'], $user->skills);
                if (!empty($hasDataScience)) {
                    $cluster = 3;
                }
            }
            
            // If no specific cluster found, assign randomly
            if ($cluster === null) {
                $cluster = rand(0, 4);
            }
            
            $user->cluster_group = $cluster;
            $user->save();
        }
        
        $this->command->info('Analytics data seeded successfully!');
        $this->command->info('Users assigned to ' . (User::whereNotNull('cluster_group')->count()) . ' clusters');
    }
}
