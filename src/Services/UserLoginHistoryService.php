<?php

namespace LoginHistory\Services;

use Core\Services\BaseService;
use LoginHistory\DTO\UserLoginHistoryCreateDTO;
use LoginHistory\Models\UserLoginHistory;

class UserLoginHistoryService extends BaseService {
    public function __construct() {
        parent::__construct(UserLoginHistory::class);
    }
}
