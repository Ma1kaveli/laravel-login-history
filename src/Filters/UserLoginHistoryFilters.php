<?php

namespace LoginHistory\Filters;

use Illuminate\Pagination\LengthAwarePaginator;
use QueryBuilder\BaseQueryBuilder;
use QueryBuilder\DTO\AvailableSort;
use QueryBuilder\DTO\AvailableSorts;

class UserLoginHistoryFilters extends BaseQueryBuilder
{
    /**
     * Дефолтный фильтр для списка
     *
     * @return LengthAwarePaginator
     */
    public function list(): LengthAwarePaginator
    {
        $this->with('user');

        $this->applyWhereHasLikeArray(
            'user',
            config('login-history.search_filter_fields') ?? [],
            'search'
        );

        $this->applyInteger('user_id');

        if (config('login-history.with_role')) {
            $this->applyWhereHasWhere('user.role', 'id', 'role_id');
        }

        if (config('login-history.with_role') && config('login-history.with_organization')) {
            $this->applyWhereHasWhere(
                'user.role',
                'organization_id'
            );
        }

        $callback = config('login-history.advanced_filters');

        if (is_callable($callback)) {
            $callback($this);
        }

        $this->applyDateStartEnd('created_at', 'date_from', 'date_to');

        $this->sortBy(['created_at'], 'id');

        $this->sortByRelationField(
            new AvailableSorts([
                new AvailableSort(
                    'users.phone',
                    'users',
                    'id',
                    'user_id'
                )
            ]),
            'users.user_login_histories'
        );

        return $this->applyPaginate();
    }
}
