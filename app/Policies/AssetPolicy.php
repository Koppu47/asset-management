<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Determine whether the user can view any models (Catalog list).
     * Guests and authenticated users can view the catalog.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Guests and authenticated users can view asset details.
     */
    public function view(?User $user, Asset $asset): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can borrow the asset.
     */
    public function borrow(User $user, Asset $asset): bool
    {
        // Admins manage assets, regular users can borrow available assets
        return ! $user->isAdmin() && $asset->isAvailable();
    }
}
