# Laravel Login History

**Laravel Login History** — это пакет для Laravel, который предоставляет расширенные возможности логирования активности авторизации пользователей. Он записывает такие данные, как идентификатор пользователя и отпечаток (fingerprint), который может содержать дополнительную информацию о сессии или устройстве, с которого выполнен вход. Пакет поддерживает асинхронное логирование, фильтрацию и получение данных истории авторизаций.

## Возможности

- Запись истории авторизаций пользователей с указанием ID и отпечатка.
- Асинхронное логирование с использованием очередей Laravel.
- Фильтрация и пагинация данных истории авторизаций.
- Консольные команды для создания тестовых данных и выполнения миграций.
- Настраиваемая поддержка ролей (опционально).
- Настраиваемая поддержка организаций (опционально).

## Требования

- PHP 8.2 или выше
- Laravel 10.10, 11.0 или 12.0
- Пакет `makaveli/laravel-query-builder` (версия 1.1.0)
- Пакет `makaveli/laravel-logger` (версия 1.1.0)

## Установка

1. Установите пакет через Composer:

   ```bash
   composer require makaveli/laravel-login-history
   ```

2. (Опционально) Опубликуйте файл конфигурации:

   ```bash
   php artisan vendor:publish --tag=login-history-config
   ```

   Файл конфигурации будет скопирован в `config/login-history.php`, где вы сможете настроить параметры, такие как модель пользователя или поддержку организаций.

3. Выполните миграции пакета:

   ```bash
   php artisan migrate:login-history
   ```

   Это создаст таблицу `users.user_login_histories` в вашей базе данных.

## Конфигурация

Файл конфигурации `config/login-history.php` позволяет настроить следующие параметры:

- **`user_model`**: Путь к модели пользователя (по умолчанию: `App\Models\User::class`).
- **`search_filter_fields`**: Поля модели пользователя по которым будет происходить поиск через параметр `search` (по умолчанию: `['name', 'patronymic', 'family_name', 'phone', 'email']`).
- **`with_role`**: Указывает, содержит ли модель пользователя связь роль (по умолчанию: `true`. Также нужно и для фильтрации по организации).
- **`advanced_filters`**: Функция в которой можно прописать кастомные фильтра. В функцию попадает @var \LoginHistory\Filters\UserLoginHistoryFilters $this
- **`with_organization`**: Указывает, содержит ли модель роли поле `organization_id` (по умолчанию: `true`).
- **`user_login_history`**: Количество тестовых записей истории авторизаций для создания через сиды (по умолчанию: `100`).
- **`transformers`**: Массив функций для преобразования параметров фильтрации при запросе списка.
- **`user_resource`**: Класс ресурса для форматирования данных пользователя в ответах API.

Пример конфигурации:

```php
return [
    'user_model' => App\Modules\Base\Models\User::class,
    'search_filter_fields' => ['name', 'patronymic', 'family_name', 'phone', 'email'],
    'with_role' => true,
    'advanced_filters' => function (\LoginHistory\Filters\UserLoginHistoryFilters $self) {
        $self->applyInteger('user_id');
    },
    'with_organization' => true,
    'user_login_history' => env('USER_LOGIN_HISTORY_FACTORY', 100),
    'transformers' => [
        'role_id' => [\App\Modules\Role\Helpers\TransformRoleFilter::class, 'getRealRoleIdFromListRequest'],
        'user_id' => [\App\Modules\User\Helpers\TransformUserFilter::class, 'getRealUserIdFromListRequest'],
        'organization_id' => [\App\Modules\Organization\Helpers\TransformOrganizationFilter::class, 'getRealOrganizationIdFromListRequest'],
    ],
    'user_resource' => [\App\Modules\Base\Resources\UserShortResource::class, 'once'],
];
```

## Использование

### Запись истории авторизации

Для записи истории авторизации пользователя используйте метод `writeLoginHistory` из трейта `LoginHistory`. Этот метод следует вызывать после успешной авторизации.

Добавьте трейт в ваш контроллер или класс, где обрабатывается авторизация:

```php
use LoginHistory\Traits\LoginHistory;

class AuthController extends Controller
{
    use LoginHistory;

    public function login(Request $request)
    {
        // Логика авторизации...

        // После успешного входа
        $this->writeLoginHistory($request);
    }
}
```

Для асинхронной записи используйте:

```php
$this->writeAsyncLoginHistory($request);
```

Это отправит задачу в очередь для фоновой обработки.

### Получение истории авторизаций

Для получения списка истории авторизаций с пагинацией используйте метод `getUserLoginHistoryList`:

```php
$paginatedList = $this->getUserLoginHistoryList($request);
```

Метод возвращает экземпляр `LengthAwarePaginator`. Для форматирования ответа в API используйте `getUserLoginHistoryListResponse`:

```php
return $this->getUserLoginHistoryListResponse($request);
```

Это вернёт ресурс `PaginatedCollection`.

### Логирования без использования trait
Для того, чтобы использовать функционал логирования без использования trait, вы можете явно инициализировать Action, Repository или Job:
```php

use LoginHistory\Repositories\UserLoginHistoryRepository;
use LoginHistory\Actions\UserLoginHistoryActions;
use LoginHistory\Jobs\JProcessLoginHistorySave;
use LoginHistory\DTO\UserLoginHistoryListDTO;
use LoginHistory\Resources\UserLoginHistoryResource;

use Illuminate\Http\Request;
use QueryBuilder\Resources\PaginatedCollection;

class AuthController extends Controller
{
    public UserLoginHistoryActions $userLoginHistoryActions;

    public UserLoginHistoryRepository $userLoginHistoryRepository;

    public function __construct() {
        $this->userLoginHistoryActions = new UserLoginHistoryActions();

        $this->userLoginHistoryRepository = new UserLoginHistoryRepository();
    }

    public function showUserLoginHistory(Request $request)
    {
        $dto = UserLoginHistoryListDTO::fromRequest($request);

        $paginatedList = $this->userLoginHistoryRepository->getPaginatedList($dto);

        return new PaginatedCollection(
            $paginatedList,
            UserLoginHistoryResource::collection($paginatedList)
        );
    }

    public function login(Request $request)
    {
        // Логика авторизации...

        // После успешного входа
        $this->userLoginHistoryActions->writeToHistory($user, $request);

        // или через очередь
        JProcessLoginHistorySave::dispatch($request, $user);
    }
}

```

### Фильтрация истории авторизаций

Класс `UserLoginHistoryListDTO` обрабатывает фильтрацию на основе параметров запроса. Поддерживаемые фильтры:

- `dateFrom` и `dateTo`: Фильтр по диапазону дат входа.
- `roleId` (если `with_role` = `true` и есть функция `transformers.role_id`): Фильтр по ID роли пользователя.
- `userId` (если есть функция `transformers.user_id`): Фильтр по ID пользователя.
- `organizationId` (если `with_role` = `true`, `with_organization` = `true` и есть функция `transformers.organization_id`): Фильтр по ID организации.
- `search`: Поиск по указанным полям пользователя.

Фильтры применяются автоматически при использовании `getUserLoginHistoryList`.

## Консольные команды

### Создание тестовых данных

Для заполнения базы тестовыми записями:

```bash
php artisan seed:test-user-login-histories
```

Количество записей определяется параметром `user_login_history` в конфигурации (по умолчанию: 100).

### Выполнение миграций

Для запуска миграций пакета:

```bash
php artisan migrate:login-history
```

## Настройка и расширение

### Трансформеры

Трансформеры позволяют изменять параметры фильтрации в зависимости от контекста авторизованного пользователя. Например, вы можете ограничить доступные роли или организации.

В конфигурации можно указать функции для `role_id`, `user_id` и `organization_id`. Эти функции должны принимать авторизованного пользователя и значение фильтра, возвращая преобразованное значение.

Пример:

```php
'transformers' => [
    'role_id' => function ($authUser, $roleId, $headCanViewAllRoles) {
        // Логика преобразования role_id
        return $transformedRoleId;
    },
    'user_id' => function ($authUser, $userId) {
        // Логика преобразования user_id
        return $transformedUserId;
    },
    'organization_id' => function ($authUser, $organizationId) {
        // Логика преобразования organization_id
        return $transformedOrganizationId;
    },
],
```

### Ресурс пользователя

По умолчанию `UserLoginHistoryResource` возвращает полную модель пользователя. Вы можете указать собственный класс ресурса:

```php
'user_resource' => [\App\Resources\UserResource::class, 'once'],
```

Это вызовет метод `UserResource::once($this->user)` для форматирования данных.

Сам `UserResource` может выглядеть следующим образом (метод `once` добавляется из-за того что мы не можем попросту прокинуть класс `new UserShortResource()` мы обязательно должны указать вызываемый метод):
```php
<?php

namespace App\Modules\Base\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserShortResource extends JsonResource {
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'family_name' => $this->family_name,
            'patronymic' => $this->patronymic,
            'email' => $this->email,
            'phone' => $this->phone,
            'big_name' => $this->bigName
        ];
    }

    public static function once($resource): static
    {
        return new static($resource);
    }
}
```

## Схема базы данных

Пакет создаёт таблицу `users.user_login_histories` со следующей структурой:

- `id`: Первичный ключ.
- `user_id`: Внешний ключ, ссылающийся на таблицу пользователей.
- `fingerprint`: Поле JSONB для хранения дополнительных данных о входе.
- `created_at` и `updated_at`: Временные метки.

## Расширение пакета

Вы можете расширить или изменить поведение пакета, переопределив:

- Модель `UserLoginHistory`.
- Класс `UserLoginHistoryFilters` для изменения логики фильтрации.
- Ресурс `UserLoginHistoryResource` для настройки формата данных.
