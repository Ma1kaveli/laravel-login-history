<?php

namespace LoginHistory\DTO;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

class UserLoginHistoryCreateDTO {
    public function __construct(
        public readonly int $userId,
        public readonly string $fingerprint
    ) {}

    /**
     * Берет из запроса фингерпринт или сохраняет пустой массив
     *
     * @param Model|Authenticatable $user
     * @param Request $request
     *
     * @return UserLoginHistoryCreateDTO
     */
    public static function fromRequest(Model|Authenticatable $user, Request $request): UserLoginHistoryCreateDTO
    {
        return new self(
            userId: $user->id,
            fingerprint: json_encode($request->get('fingerprint', [])),
        );
    }

    /**
     * Summary of toArray
     *
     * @return array{fingerprint: string, user_id: int}
     */
    public function toArray(): array {
        return [
            'user_id' => $this->userId,
            'fingerprint' => $this->fingerprint
        ];
    }
}
