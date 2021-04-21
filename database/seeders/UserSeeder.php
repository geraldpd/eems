<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	foreach(config('eems.roles') as $role) {
	        $user = User::create([
				'firstname' => $role,
                'lastname' => $role,
                'mobile_number' => '900000000'.rand(0,9),
				'email' => Str::lower($role.'@'.config('app.name').'.com'),
				'email_verified_at' => Carbon::now(),
				'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
	        ]);
	        $user->assignRole($role);
		}
    }
}
