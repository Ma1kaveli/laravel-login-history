<?php

namespace LoginHistory\Services;

use LoginHistory\DTO\UserLoginHistoryCreateDTO;
use LoginHistory\Models\UserLoginHistory;

class UserLoginHistoryService {
    public function __construct() {}

    /**
     * Добавление новой записи об авторизации пользователя
     *
     * @param  UserLoginHistoryCreateDTO $dto
     *
     * @return UserLoginHistory
     */
    public function create(UserLoginHistoryCreateDTO $dto): UserLoginHistory
    {
        return UserLoginHistory::create([
            'user_id' => $dto->userId,
            'fingerprint' => $dto->fingerprint,
        ]);
    }
}
