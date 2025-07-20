<?php

namespace LoginHistory\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginHistoryResource extends JsonResource {
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'fingerprint' => $this->fingerprint,
            'created_at' => $this->created_at,
            'user' => config('login-history.user_resource')
                ? call_user_func(config('login-history.user_resource'), $this->user)
                : $this->user,
        ];
    }
}
