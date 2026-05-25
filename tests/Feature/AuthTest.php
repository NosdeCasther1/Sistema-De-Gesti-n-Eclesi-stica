<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the login form is displayed successfully.
     */
    public function test_login_form_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Iniciar Sesión Oficial');
        $response->assertSee('Gestión Ministerial');
    }

    /**
     * Test a user can log in with valid credentials.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = Usuario::create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    /**
     * Test a user cannot log in with invalid credentials.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = Usuario::create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test a user can log out.
     */
    public function test_users_can_logout(): void
    {
        $user = Usuario::create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
