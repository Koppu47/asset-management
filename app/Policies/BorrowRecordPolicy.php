<?php

namespace App\Policies;

use App\Models\BorrowRecord;
use App\Models\User;

class BorrowRecordPolicy
{
    /**
     * Determine whether the user can view any models (Admin borrow management).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->isAdmin() || $user->id === $borrowRecord->user_id;
    }

    /**
     * Determine whether the user can cancel the borrow request.
     */
    public function cancel(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->id === $borrowRecord->user_id && $borrowRecord->status === 'pending';
    }

    /**
     * Determine whether the user can approve the borrow request.
     */
    public function approve(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->isAdmin() && $borrowRecord->status === 'pending';
    }

    /**
     * Determine whether the user can reject the borrow request.
     */
    public function reject(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->isAdmin() && $borrowRecord->status === 'pending';
    }

    /**
     * Determine whether the user can process returning the asset.
     */
    public function return(User $user, BorrowRecord $borrowRecord): bool
    {
        return $user->isAdmin() && $borrowRecord->status === 'approved';
    }
}
