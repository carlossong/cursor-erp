<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    public function view(User $user, Customer $customer): Response
    {
        return $this->authorizeForCustomer($user, $customer, 'customers.view');
    }

    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    public function update(User $user, Customer $customer): Response
    {
        return $this->authorizeForCustomer($user, $customer, 'customers.update');
    }

    public function delete(User $user, Customer $customer): Response
    {
        return $this->authorizeForCustomer($user, $customer, 'customers.delete');
    }

    private function authorizeForCustomer(User $user, Customer $customer, string $permission): Response
    {
        if ($user->company_id !== $customer->company_id) {
            return Response::denyAsNotFound();
        }

        return $user->can($permission)
            ? Response::allow()
            : Response::deny();
    }
}
