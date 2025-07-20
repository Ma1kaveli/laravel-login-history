<?php

namespace LoginHistory\Database\Seeders\Test;

use LoginHistory\Models\UserLoginHistory;

use Illuminate\Database\Seeder;

class UserLoginHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserLoginHistory::factory(config('login-history.user_login_history'))->create();
    }
}
