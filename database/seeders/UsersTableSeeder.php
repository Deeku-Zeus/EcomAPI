<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EcomBackend\{User,UserProfile};
use Illuminate\Support\Facades\{Hash,DB};

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('user_profiles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Create multiple users with specific data
        $usersData = [
            [
                'first_name' => 'Ecom',
                'last_name' => 'Media',
                'username' => 'ecom_media',
                'email' => 'admin@ecom.media',
                'phone_number' => '9123123123',
                'password' => Hash::make('Rakuten@123')
            ]
        ];

        foreach ($usersData as $userData) {
            // Create the user
            $user = User::create($userData);

            // Create profiles for each user
            $profiles = ['deeku', 'reflector', 'nish'];
            foreach ($profiles as $profile) {
                UserProfile::create([
                    'user_id' => $user->id,
                    'profile_name' => $profile,
                ]);
            }
        }
    }
}
