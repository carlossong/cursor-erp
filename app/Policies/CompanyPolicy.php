<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('companies.view');
    }

    public function view(User $user, Company $company): Response
    {
        return $this->authorizeForCompany($user, $company, 'companies.view');
    }

    public function create(User $user): bool
    {
        return $user->can('companies.create');
    }

    public function update(User $user, Company $company): Response
    {
        return $this->authorizeForCompany($user, $company, 'companies.update');
    }

    public function delete(User $user, Company $company): Response
    {
        return $this->authorizeForCompany($user, $company, 'companies.delete');
    }

    private function authorizeForCompany(User $user, Company $company, string $permission): Response
    {
        if ($user->company_id !== $company->id) {
            return Response::denyAsNotFound();
        }

        return $user->can($permission)
            ? Response::allow()
            : Response::deny();
    }
}
