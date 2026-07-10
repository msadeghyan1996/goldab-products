<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function viewAny(Admin $actor): bool
    {
        return $actor->is_active;
    }

    public function view(Admin $actor, Admin $admin): bool
    {
        return $actor->is_active;
    }

    public function create(Admin $actor): bool
    {
        return $actor->is_active;
    }

    public function update(Admin $actor, Admin $admin): bool
    {
        return $actor->is_active;
    }

    public function delete(Admin $actor, Admin $admin): bool
    {
        return $actor->is_active;
    }
}
