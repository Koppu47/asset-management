<?php

namespace App\Policies;

use App\Models\InventorySurvey;
use App\Models\User;

class InventorySurveyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventorySurvey $inventorySurvey): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update survey items or complete the survey.
     */
    public function update(User $user, InventorySurvey $inventorySurvey): bool
    {
        return $user->isAdmin();
    }
}
