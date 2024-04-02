<?php

namespace App\Policies;

use App\Models\EmployeeSeminartraining;
use App\Models\User;

class EmployeeSeminartrainingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeSeminartraining $employeeSeminartraining): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeeSeminartraining $employeeSeminartraining): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeSeminartraining $employeeSeminartraining): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployeeSeminartraining $employeeSeminartraining): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployeeSeminartraining $employeeSeminartraining): bool
    {
        //
    }
}
