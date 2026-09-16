<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;


class TaskPolicy
{
    public function view(User $user, Task $task) : bool
    {
        return $user->id == $task->user_id;
    }

     public function update(User $user, Task $task) : bool
    {
        return $user->id == $task->user_id;
    }

     public function delete(User $user, Task $task) : bool
    {
        return $user->id == $task->user_id;
    }
}
