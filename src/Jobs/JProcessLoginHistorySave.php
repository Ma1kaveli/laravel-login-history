<?php

namespace LoginHistory\Jobs;

use LoginHistory\Actions\UserLoginHistoryActions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use Logger\Facades\LaravelLog;

use function Laravel\Prompts\error;

class JProcessLoginHistorySave implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    protected mixed $request;

    /**
     * @var Authenticatable
     */
    protected Authenticatable $user;

    protected UserLoginHistoryActions $userLoginHistoryActions;

    /**
     * Количество секунд, в течение которых задание может выполняться до истечения тайм-аута.
     *
     * @var int
     */
    public $timeout = 36000;

    /**
     * Create a new job instance.
     *
     * @param mixed $request
     * @param Authenticatable $user
     *
     * @return void
     */
    public function __construct( mixed $request, Authenticatable $user) {
        $this->request = $request;

        $this->user = $user;

        $this->userLoginHistoryActions = new UserLoginHistoryActions();
    }

    public function failed(\Throwable $exception)
    {
        LaravelLog::critical('JProcessLoginHistorySave: Job FAILED with error: ' . $exception->getMessage());
        // Send notification to admin, etc.
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Auth::setUser($this->user);
        LaravelLog::info('JProcessLoginHistorySave: Starting job...');
        try {
            $this->userLoginHistoryActions->writeToHistory($this->user, $this->request);

            LaravelLog::info('JProcessLoginHistorySave: Job completed successfully.');
        } catch (\Exception $e) {
            LaravelLog::error('JProcessLoginHistorySave: Job failed with error: ' . $e->getMessage());
            throw $e;
        }
    }
}
