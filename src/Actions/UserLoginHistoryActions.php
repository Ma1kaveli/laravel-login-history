<?php

namespace LoginHistory\Actions;

use LoginHistory\DTO\UserLoginHistoryCreateDTO;
use LoginHistory\Services\UserLoginHistoryService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Logger\Facades\LaravelLog;

class UserLoginHistoryActions {

    public UserLoginHistoryService $userLoginHistoryService;

    public function __construct()
    {
        $this->userLoginHistoryService = new UserLoginHistoryService();
    }

    /**
     * Записываем в историю авторизацию пользователя
     *
     * @param Model|Authenticatable $user
     * @param mixed $request
     *
     * @return void
     */
    public function writeToHistory(Model|Authenticatable $user, mixed $request): void
    {
        try {
            $this->userLoginHistoryService->create(
                UserLoginHistoryCreateDTO::fromRequest($user, $request)->toArray()
            );
        } catch (\Exception $e) {
            LaravelLog::error($e);
        }
    }

}
