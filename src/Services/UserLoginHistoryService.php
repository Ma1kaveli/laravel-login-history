<?php

namespace LoginHistory\Services;

use Core\Services\BaseService;
use LoginHistory\DTO\UserLoginHistoryCreateDTO;
use LoginHistory\Models\UserLoginHistory;

class UserLoginHistoryService extends BaseService {
    public function __construct() {
        parent::__construct(UserLoginHistory::class);
    }

    /**
     * Добавление новой записи об авторизации пользователя
     *
     * @param  UserLoginHistoryCreateDTO $dto
     *
     * @return UserLoginHistory
     */
    public function _create(UserLoginHistoryCreateDTO $dto): UserLoginHistory
    {
        return $this->create([
            'user_id' => $dto->userId,
            'fingerprint' => $dto->fingerprint,
        ]);
    }
}
