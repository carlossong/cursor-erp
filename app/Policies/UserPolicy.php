<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): Response
    {
        return $this->authorizeForUser($user, $model, 'users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): Response
    {
        return $this->authorizeForUser($user, $model, 'users.update');
    }

    public function delete(User $user, User $model): Response
    {
        return $this->authorizeForUser($user, $model, 'users.delete');
    }

    private function authorizeForUser(User $user, User $model, string $permission): Response
    {
        if ($user->company_id !== $model->company_id) {
            return Response::denyAsNotFound();
        }

        return $user->can($permission)
            ? Response::allow()
            : Response::deny();
    }
}
