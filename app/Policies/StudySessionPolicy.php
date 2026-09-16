<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudySession;

class StudySessionPolicy
{
   
  public function view(User $user,StudySession $studysession) : bool {

    return $user->id === $studysession->user_id;

  }

  public function update(User $user,StudySession $studysession) : bool {
    return $user->id === $studysession->user_id;
  }




}
