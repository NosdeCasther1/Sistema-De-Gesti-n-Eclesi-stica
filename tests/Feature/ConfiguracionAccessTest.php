<?php

namespace Tests\Feature;

use Tests\TestCase;

class ConfiguracionAccessTest extends TestCase
{
    public function test_non_admin_cannot_access_configuracion_even_with_dynamic_module_permission(): void
    {
        $response = $this
            ->withSession([
                'current_rol' => 'tesorero',
                'role_permissions' => [
                    'tesorero' => ['configuracion'],
                ],
            ])
            ->get('/configuracion');

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }
}
