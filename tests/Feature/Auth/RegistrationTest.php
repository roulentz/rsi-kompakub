<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_new_users_can_register_and_must_verify_code(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'tanggal_lahir' => '2004-05-10',
            'email' => 'test@student.ub.ac.id',
            'no_telepon' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('verifikasi.form', absolute: false));
        $this->assertDatabaseHas('users', ['email' => 'test@student.ub.ac.id']);
    }

    public function test_non_campus_email_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'tanggal_lahir' => '2004-05-10',
            'email' => 'test@gmail.com',
            'no_telepon' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'test@gmail.com']);
    }
}