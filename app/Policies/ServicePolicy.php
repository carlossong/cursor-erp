<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('services.view');
    }

    public function view(User $user, Service $service): Response
    {
        return $this->authorizeForService($user, $service, 'services.view');
    }

    public function create(User $user): bool
    {
        return $user->can('services.create');
    }

    public function update(User $user, Service $service): Response
    {
        return $this->authorizeForService($user, $service, 'services.update');
    }

    public function delete(User $user, Service $service): Response
    {
        return $this->authorizeForService($user, $service, 'services.delete');
    }

    public function viewCost(User $user, Service $service): Response
    {
        return $this->authorizeForService($user, $service, 'services.view-cost');
    }

    private function authorizeForService(User $user, Service $service, string $permission): Response
    {
        if ($user->company_id !== $service->company_id) {
            return Response::denyAsNotFound();
        }

        return $user->can($permission)
            ? Response::allow()
            : Response::deny();
    }
}
