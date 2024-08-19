<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 授权策略
     * 只允许当前用户删除自己的微博
     *
     * @param User $user
     * @param Status $status
     * @return bool
     */
    public function destroy(User $user, Status $status): bool
    {
        return $user->id === $status->user_id;
    }
}
