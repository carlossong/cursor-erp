<?php

namespace App\Policies;

use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('services.view');
    }

    public function view(User $user, ServiceCategory $serviceCategory): Response
    {
        return $this->authorizeForCategory($user, $serviceCategory, 'services.view');
    }

    public function create(User $user): bool
    {
        return $user->can('services.create');
    }

    public function update(User $user, ServiceCategory $serviceCategory): Response
    {
        return $this->authorizeForCategory($user, $serviceCategory, 'services.update');
    }

    public function delete(User $user, ServiceCategory $serviceCategory): Response
    {
        return $this->authorizeForCategory($user, $serviceCategory, 'services.delete');
    }

    private function authorizeForCategory(User $user, ServiceCategory $serviceCategory, string $permission): Response
    {
        if ($user->company_id !== $serviceCategory->company_id) {
            return Response::denyAsNotFound();
        }

        return $user->can($permission)
            ? Response::allow()
            : Response::deny();
    }
}
