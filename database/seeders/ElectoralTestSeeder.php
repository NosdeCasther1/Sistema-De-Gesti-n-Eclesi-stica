<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizacion;
use App\Models\Eleccion;
use App\Models\Candidato;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElectoralTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar datos previos de prueba (Opcional, previene duplicados en pruebas)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Candidato::truncate();
        Eleccion::truncate();
        DB::table('miembro_organizacion')->truncate();
        Organizacion::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Crear Organización
        $cajaJovenes = DB::table('financial_accounts')->where('name', 'Caja de Jóvenes')->first();

        $org = Organizacion::create([
            'nombre' => 'Sociedad de Jóvenes "Gedeón"',
            'descripcion' => 'Ministerio enfocado en el crecimiento espiritual de adolescentes y jóvenes adultos.',
            'estado' => true,
            'financial_account_id' => $cajaJovenes ? $cajaJovenes->id : null,
        ]);

        // 3. Garantizar que existan miembros en la base de datos
        $familiaId = DB::table('familias')->insertGetId([
            'nombre' => 'Familia Test Electoral',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        $nombres = ['Erick Gómez', 'Débora Martínez', 'Samuel López', 'Rebeca Vásquez', 'Daniel Castillo', 'Mateo Pérez', 'Esteban Ramírez'];
        $miembrosIds = [];
        
        foreach ($nombres as $nombreCompleto) {
            $partes = explode(' ', $nombreCompleto);
            // Inserta ignorando si el esquema exige otros campos que puedan tener default/nullable
            $miembrosIds[] = DB::table('miembros')->insertGetId([
                'familia_id' => $familiaId,
                'nombres' => $partes[0],
                'apellidos' => $partes[1] ?? '',
                'estado' => 1,
                'etapa_consolidacion' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // 4. Asociar miembros al padrón (miembro_organizacion)
        foreach ($miembrosIds as $id) {
            DB::table('miembro_organizacion')->insert([
                'miembro_id' => $id,
                'organizacion_id' => $org->id,
                'puesto' => 'Miembro',
                'estado' => 1,
                'fecha_asignacion' => Carbon::now(),
            ]);
        }

        // 5. Crear Elección Activa
        $eleccion = Eleccion::create([
            'organizacion_id' => $org->id,
            'titulo' => 'Elección Directiva Jóvenes 2026',
            'fecha_inicio' => Carbon::now()->subMinutes(15), // Inició hace 15 min
            'fecha_fin' => Carbon::now()->addHours(2), // Cierra en 2 horas
            'estado' => 'activa',
        ]);

        // 6. Crear Candidatos a Presidente
        $candidatosPresidente = [4, 5, 6]; // Daniel, Mateo, Esteban
        foreach ($candidatosPresidente as $index) {
            Candidato::create([
                'eleccion_id' => $eleccion->id,
                'miembro_id' => $miembrosIds[$index],
                'puesto_postulado' => 'Presidente',
            ]);
        }
        
        // 7. Crear Candidatos a otros puestos
        Candidato::create(['eleccion_id' => $eleccion->id, 'miembro_id' => $miembrosIds[0], 'puesto_postulado' => 'Vicepresidente']);
        Candidato::create(['eleccion_id' => $eleccion->id, 'miembro_id' => $miembrosIds[1], 'puesto_postulado' => 'Secretario']);
        Candidato::create(['eleccion_id' => $eleccion->id, 'miembro_id' => $miembrosIds[2], 'puesto_postulado' => 'Tesorero']);
    }
}
