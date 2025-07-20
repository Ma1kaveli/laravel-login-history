<?php

namespace LoginHistory\Console\Commands;

use Illuminate\Console\Command;

class CMigrateLoginHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:login-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run package migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('migrate', [
            '--path' => 'vendor/makaveli/laravel-login-history/src/Database/migrations',
            '--force' => true
        ]);
    }
}