<?php

namespace LoginHistory\Console\Commands;

use LoginHistory\Database\Seeders\Test\UserLoginHistorySeeder;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CLoadTestUserLoginHistories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:test-user-login-histories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска сида для загрузки тестовых историй авторизации пользователей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $this->info('Загрузка тестовых историй авторизации пользователей...');
            $this->call(UserLoginHistorySeeder::class);
            $this->info('Успешно!');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
        return 1;
    }
}
