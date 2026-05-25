<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Organizacion;
use App\Models\Eleccion;
use App\Models\Miembro;
use App\Models\Candidato;
use App\Models\Familia;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReporteVotacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_generate_escrutinio_report(): void
    {
        $user = Usuario::create([
            'nombre' => 'Admin Test',
            'email' => 'admin_test@iglesia.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('administrador');

        $familia = Familia::create([
            'nombre' => 'Familia Perez'
        ]);

        $organizacion = Organizacion::create([
            'nombre' => 'Sociedad de Jóvenes',
            'descripcion' => 'Jóvenes AD',
            'estado' => true,
        ]);

        $eleccion = Eleccion::create([
            'organizacion_id' => $organizacion->id,
            'titulo' => 'Elección Directiva 2026',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addHour(),
            'estado' => 'finalizada',
            'tipo_mayoria' => 'simple',
            'puesto_en_curso' => 'Presidente',
        ]);

        $miembro1 = Miembro::create([
            'familia_id' => $familia->id,
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'dpi' => '1234567890101',
            'sexo' => 'M',
            'estado' => true,
        ]);
        
        $miembro2 = Miembro::create([
            'familia_id' => $familia->id,
            'nombres' => 'Maria',
            'apellidos' => 'Gomez',
            'dpi' => '1234567890102',
            'sexo' => 'F',
            'estado' => true,
        ]);

        $organizacion->miembros()->attach($miembro1->id, ['puesto' => 'Miembro', 'fecha_asignacion' => now(), 'estado' => true]);
        $organizacion->miembros()->attach($miembro2->id, ['puesto' => 'Miembro', 'fecha_asignacion' => now(), 'estado' => true]);

        $candidato = Candidato::create([
            'eleccion_id' => $eleccion->id,
            'miembro_id' => $miembro1->id,
            'puesto_postulado' => 'Presidente',
        ]);

        $candidato->votos_digitales = 10;
        $candidato->votos_manuales = 5;
        $candidato->save();

        DB::table('registro_votantes')->insert([
            'eleccion_id' => $eleccion->id,
            'miembro_id' => $miembro2->id,
            'puesto_votado' => 'Presidente',
            'modalidad' => 'digital',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->get(route('reportes.votaciones.escrutinio', $eleccion));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_generate_participantes_report(): void
    {
        $user = Usuario::create([
            'nombre' => 'Admin Test',
            'email' => 'admin_test@iglesia.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('administrador');

        $familia = Familia::create([
            'nombre' => 'Familia Perez'
        ]);

        $organizacion = Organizacion::create([
            'nombre' => 'Sociedad de Jóvenes',
            'descripcion' => 'Jóvenes AD',
            'estado' => true,
        ]);

        $eleccion = Eleccion::create([
            'organizacion_id' => $organizacion->id,
            'titulo' => 'Elección Directiva 2026',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addHour(),
            'estado' => 'finalizada',
            'tipo_mayoria' => 'simple',
            'puesto_en_curso' => 'Presidente',
        ]);

        $miembro = Miembro::create([
            'familia_id' => $familia->id,
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'dpi' => '1234567890101',
            'sexo' => 'M',
            'estado' => true,
        ]);

        $organizacion->miembros()->attach($miembro->id, ['puesto' => 'Miembro', 'fecha_asignacion' => now(), 'estado' => true]);

        DB::table('registro_votantes')->insert([
            'eleccion_id' => $eleccion->id,
            'miembro_id' => $miembro->id,
            'puesto_votado' => 'Presidente',
            'modalidad' => 'digital',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->get(route('reportes.votaciones.participantes', $eleccion));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_generate_conformacion_report(): void
    {
        $user = Usuario::create([
            'nombre' => 'Admin Test',
            'email' => 'admin_test@iglesia.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('administrador');

        $familia = Familia::create([
            'nombre' => 'Familia Perez'
        ]);

        $organizacion = Organizacion::create([
            'nombre' => 'Sociedad de Jóvenes',
            'descripcion' => 'Jóvenes AD',
            'estado' => true,
        ]);

        $eleccion = Eleccion::create([
            'organizacion_id' => $organizacion->id,
            'titulo' => 'Elección Directiva 2026',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addHour(),
            'estado' => 'finalizada',
            'tipo_mayoria' => 'simple',
            'puesto_en_curso' => 'Presidente',
        ]);

        $miembro = Miembro::create([
            'familia_id' => $familia->id,
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'dpi' => '1234567890101',
            'sexo' => 'M',
            'estado' => true,
        ]);

        $organizacion->miembros()->attach($miembro->id, ['puesto' => 'Miembro', 'fecha_asignacion' => now(), 'estado' => true]);

        $candidato = Candidato::create([
            'eleccion_id' => $eleccion->id,
            'miembro_id' => $miembro->id,
            'puesto_postulado' => 'Presidente',
        ]);
        $candidato->votos_digitales = 5;
        $candidato->save();

        $response = $this
            ->actingAs($user)
            ->withSession([
                'current_rol' => 'administrador',
            ])
            ->get(route('reportes.votaciones.conformacion', $eleccion));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
