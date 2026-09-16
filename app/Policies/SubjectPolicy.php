<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subject;

class SubjectPolicy
{
    public function view(User $user, Subject $subject): bool
    {
        return $user->id === $subject->user_id;
    }

    public function update(User $user, Subject $subject): bool
    {
        return $user->id === $subject->user_id;
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->id === $subject->user_id;
    }
}
