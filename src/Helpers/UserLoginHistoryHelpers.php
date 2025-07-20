<?php

namespace LoginHistory\Helpers;

class UserLoginHistoryHelpers {
    /**
     * Получаем закодированый json
     *
     * @return string
     */
    public static function getJsonb()
    {
        return json_encode([
            'test' => 'test'
        ]);
    }
}