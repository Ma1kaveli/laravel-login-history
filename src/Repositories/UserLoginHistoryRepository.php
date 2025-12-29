<?php

namespace LoginHistory\Repositories;

use Core\Repositories\BaseRepository;
use LoginHistory\Models\UserLoginHistory;

class UserLoginHistoryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct(UserLoginHistory::class);
    }
}
