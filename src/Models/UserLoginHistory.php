<?php

namespace LoginHistory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use LoginHistory\Database\Factories\UserLoginHistoryFactory;
use LoginHistory\Filters\UserLoginHistoryFilters;
use QueryBuilder\Traits\Filterable;

class UserLoginHistory extends Model
{
    use HasFactory, Filterable;

    protected $table = 'users.user_login_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'fingerprint',
        'created_at',
    ];

    /**
     * Устанавливаем класс фильтрации, теперь мы можем использовать методы фильтрации, как
     *      $this->model->filter($dto->params)->list();
     *
     * @return string
     */
    public static function getFilterClass(): string {
        return UserLoginHistoryFilters::class;
    }

    /**
     * Меняем фабрику, за место той, которую ищут по умолчанию
     *
     * @return mixed
     */
    protected static function newFactory()
    {
        return UserLoginHistoryFactory::new(); // Указываем фабрику
    }

    /**
     * user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            config('logger.user_model'),
            'user_id',
            'id'
        );
    }
}
