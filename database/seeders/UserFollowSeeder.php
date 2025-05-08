<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Novel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserFollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all novels with their follower counts
        $novels = Novel::select('id', 'followers')->get();
        $userIds = User::pluck('id')->toArray();
        $totalUsers = count($userIds);
        
        // For each novel, create the appropriate number of user follow records
        foreach ($novels as $novel) {
            // Get the number of followers for this novel
            $followersCount = $novel->followers;
            
            // Skip if followers count is 0
            if ($followersCount <= 0) {
                continue;
            }
            
            // Make sure we don't try to create more followers than we have users
            $followersCount = min($followersCount, $totalUsers);
            
            // Randomly select users to follow this novel
            $shuffledUserIds = $userIds;
            shuffle($shuffledUserIds);
            $selectedUserIds = array_slice($shuffledUserIds, 0, $followersCount);
            
            // Create the follow relationships
            $followData = [];
            foreach ($selectedUserIds as $userId) {
                $followData[] = [
                    'user_id' => $userId,
                    'novel_id' => $novel->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            // Insert the follow data in batches to improve performance
            if (!empty($followData)) {
                DB::table('user_follows')->insert($followData);
            }
        }
    }
}
