<?php

namespace LoginHistory\DTO;

use Illuminate\Http\Request;
use QueryBuilder\DTO\ListDTO;

class UserLoginHistoryListDTO {
    public function __construct(
        public readonly array $params,
    ) {}

    /**
     * @param Request $request
     *
     * @return UserLoginHistoryListDTO
     */
    public static function fromRequest(Request $request): UserLoginHistoryListDTO {
        $config = config('login-history');

        $allowedParams = ['dateFrom', 'dateTo', 'roleId', 'userId'];
        if ($config['with_organization']) {
            $allowedParams[] = 'organizationId';
        }

        $dto = ListDTO::fromRequest(
            $request,
            $allowedParams,
            [ 'phone' => 'users.phone' ]
        );

        $transformers = $config['transformers'] ?? [];
        $processedParams = [
            ...$dto->params,
            'created_by' => null,
            'role_id' => null,
            'organization_id' => null,
        ];

        if ($config['with_role'] && isset($transformers['role_id'])) {
            $processedParams['role_id'] = call_user_func(
                $transformers['role_id'],
                $dto->params['auth_user'],
                $dto->params['role_id'],
                true
            );
        }

        if (isset($transformers['user_id'])) {
            $processedParams['created_by'] = call_user_func(
                $transformers['user_id'],
                $dto->params['auth_user'],
                $dto->params['user_id']
            );
        }

        if ($config['with_organization'] && isset($transformers['organization_id'])) {
            $processedParams['organization_id'] = call_user_func(
                $transformers['organization_id'],
                $dto->params['auth_user'],
                $dto->params['organization_id']
            );
        }

        return new self(
            params: $processedParams,
        );
    }
}
