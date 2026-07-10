<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminService
{
    public function create(array $data): Admin
    {
        $data['password'] = Hash::make($data['password']);

        return Admin::create($data);
    }

    public function update(Admin $admin, array $data, Admin $actor): Admin
    {
        if ($admin->is($actor) && ! $data['is_active']) {
            throw ValidationException::withMessages(['is_active' => 'نمی‌توانید حساب کاربری خودتان را غیرفعال کنید.']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $admin->update($data);

        return $admin;
    }

    public function delete(Admin $admin, Admin $actor): void
    {
        if ($admin->is($actor)) {
            throw ValidationException::withMessages(['admin' => 'حذف حساب کاربری خودتان مجاز نیست.']);
        }

        if ($admin->is_active && Admin::where('is_active', true)->count() <= 1) {
            throw ValidationException::withMessages(['admin' => 'آخرین مدیر فعال قابل حذف نیست.']);
        }

        $admin->delete();
    }
}
