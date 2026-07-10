<?php

use App\Models\Admin;
use Illuminate\Support\Facades\DB;

test('guest is redirected to mobile login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('active admin can login with mobile and password', function () {
    $admin = Admin::factory()->create(['mobile' => '09121111111', 'password' => 'secret123']);

    $this->post('/login', ['mobile' => $admin->mobile, 'password' => 'secret123', 'remember' => false])
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($admin);
});

test('inactive admin cannot login', function () {
    $admin = Admin::factory()->inactive()->create(['mobile' => '09122222222', 'password' => 'secret123']);

    $this->post('/login', ['mobile' => $admin->mobile, 'password' => 'secret123'])
        ->assertSessionHasErrors('mobile');
    $this->assertGuest();
});

test('malformed password hash does not expose a server error', function () {
    DB::table('admins')->insert([
        'name' => 'Broken Hash',
        'mobile' => '09123333333',
        'password' => 'not-a-valid-password-hash',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->post('/login', ['mobile' => '09123333333', 'password' => 'secret123'])
        ->assertSessionHasErrors('mobile');

    $this->assertGuest();
});
