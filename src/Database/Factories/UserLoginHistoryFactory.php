<?php

namespace LoginHistory\Database\Factories;

use LoginHistory\Models\UserLoginHistory;
use LoginHistory\Helpers\UserLoginHistoryHelpers;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\LoginHistory\Models\UserLoginHistory>
 */
class UserLoginHistoryFactory extends Factory
{
    protected $model = UserLoginHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => config('logger.user_model')::inRandomOrder()->first()->id,
            'fingerprint' => UserLoginHistoryHelpers::getJsonb(),
        ];
    }
}
