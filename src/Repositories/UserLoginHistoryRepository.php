<?php

namespace LoginHistory\Repositories;

use LoginHistory\Models\UserLoginHistory;

use QueryBuilder\Repositories\BaseRepository;

class UserLoginHistoryRepository extends BaseRepository {
    public function __construct() {
        parent::__construct(new UserLoginHistory());
    }
}
