<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::SELLER);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->hasRole(Role::SELLER) &&
            $user->businesses()->where('businesses.id', $product->business_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Role::SELLER) && $user->businesses()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->hasRole(Role::SELLER) &&
            $user->businesses()->where('businesses.id', $product->business_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole(Role::SELLER) &&
            $user->businesses()->where('businesses.id', $product->business_id)->exists();
    }

    /**
     * Determine whether the user can toggle the model status.
     */
    public function toggle(User $user, Product $product): bool
    {
        return $user->hasRole(Role::SELLER) &&
            $user->businesses()->where('businesses.id', $product->business_id)->exists();
    }
}
