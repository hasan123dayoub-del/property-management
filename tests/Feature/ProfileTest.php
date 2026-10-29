<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

// The profile lives in the dashboard, which only admins and employees can open.
function dashboardUser(): User
{
    Role::findOrCreate('employee', 'web');

    return User::factory()->create()->assignRole('employee');
}

test('profile page is displayed', function () {
    $user = dashboardUser();

    $response = $this
        ->actingAs($user)
        ->get('/dashboard/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = dashboardUser();

    $response = $this
        ->actingAs($user)
        ->patch('/dashboard/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/dashboard/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = dashboardUser();

    $response = $this
        ->actingAs($user)
        ->patch('/dashboard/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/dashboard/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = dashboardUser();

    $response = $this
        ->actingAs($user)
        ->delete('/dashboard/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = dashboardUser();

    $response = $this
        ->actingAs($user)
        ->from('/dashboard/profile')
        ->delete('/dashboard/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/dashboard/profile');

    $this->assertNotNull($user->fresh());
});
