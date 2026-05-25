<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfiguracionAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_non_admin_cannot_access_configuracion(): void
    {
        $user = Usuario::create([
            'nombre' => 'Tesorero Test',
            'email' => 'tesorero@test.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('tesorero');

        $response = $this->actingAs($user)->get('/configuracion');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_configuracion(): void
    {
        $admin = Usuario::create([
            'nombre' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('administrador');

        $response = $this->actingAs($admin)->get('/configuracion');

        $response->assertStatus(200);
    }
}
