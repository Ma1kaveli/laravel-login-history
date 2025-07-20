<?php

namespace LaravelHistory\Traits;

use LoginHistory\Actions\UserLoginHistoryActions;
use LoginHistory\DTO\UserLoginHistoryListDTO;
use LoginHistory\Jobs\JProcessLoginHistorySave;
use LoginHistory\Repositories\UserLoginHistoryRepository;
use LoginHistory\Resources\UserLoginHistoryResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use QueryBuilder\Resources\PaginatedCollection;

trait LoginHistory {

    /**
     * writeHistory
     *
     * @param mixed $request
     *
     * @return void
     */
    public function writeLoginHistory(mixed $request): void
    {
        $userLoginHistoryActions = new UserLoginHistoryActions();
        $user = Auth::user();

        $userLoginHistoryActions->writeToHistory($user, $request);
    }

    /**
     * writeAsyncLoginHistory
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function writeAsyncLoginHistory(mixed $request): void
    {
        $user = Auth::user();

        JProcessLoginHistorySave::dispatch($request, $user);
    }

    /**
     * getUserLoginHistoryList
     *
     * @param Request $request
     *
     * @return LengthAwarePaginator
     */
    public function getUserLoginHistoryList(Request $request): LengthAwarePaginator
    {
        $userLoginHistoryRepository = new UserLoginHistoryRepository();

        $dto = UserLoginHistoryListDTO::fromRequest($request);

        $paginatedList = $userLoginHistoryRepository->getPaginatedList($dto);

        return $paginatedList;
    }

    /**
     * getUserLoginHistoryListResponse
     *
     * @param Request $request
     *
     * @return PaginatedCollection
     */
    public function getUserLoginHistoryListResponse(Request $request): PaginatedCollection
    {
        $paginatedList = $this->userLoginHistoryList($request);

        return new PaginatedCollection(
            $paginatedList,
            UserLoginHistoryResource::collection($paginatedList)
        );
    }
}
