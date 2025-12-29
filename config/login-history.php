<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User model path
    |--------------------------------------------------------------------------
    */
    'user_model' => App\Modules\Base\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | User model fields which we need search
    |--------------------------------------------------------------------------
    */
    'search_filter_fields' => ['name', 'patronymic', 'family_name', 'phone', 'email'],

    /*
    |--------------------------------------------------------------------------
    | User Has Role relation
    |--------------------------------------------------------------------------
    */
    'with_role' => true,

    /*
    |--------------------------------------------------------------------------
    | Advanced filters
    |--------------------------------------------------------------------------
    */
    'advanced_filters' => null,

    /*
    |--------------------------------------------------------------------------
    | Has organization_id in Role model
    |--------------------------------------------------------------------------
    */
    'with_organization' => true,

    /*
    |--------------------------------------------------------------------------
    | Count create user_login_histories in factory
    |--------------------------------------------------------------------------
    */
    'user_login_history' => env('USER_LOGIN_HISTORY_FACTORY', 100),

    /*
    |--------------------------------------------------------------------------
    | Validation methods for List DTO params
    |--------------------------------------------------------------------------
    */
    'transformers' => [
        'role_id' => [\App\Modules\Role\Helpers\TransformRoleFilter::class, 'getRealRoleIdFromListRequest'],
        'user_id' => [\App\Modules\User\Helpers\TransformUserFilter::class, 'getRealUserIdFromListRequest'],
        'organization_id' => [\App\Modules\Organization\Helpers\TransformOrganizationFilter::class, 'getRealOrganizationIdFromListRequest'],
    ],

    /*
    |--------------------------------------------------------------------------
    | User resource model for UserLoginHistoryResource
    |--------------------------------------------------------------------------
    */
    'user_resource' => [\App\Modules\Base\Resources\UserShortResource::class, 'once'],
];
