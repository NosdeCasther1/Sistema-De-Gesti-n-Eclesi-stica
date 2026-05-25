<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CleanAndSeedMembersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Desactivar llaves foráneas y truncar/limpiar tablas relacionadas
        Schema::disableForeignKeyConstraints();
        DB::table('miembro_ministerio')->truncate();
        DB::table('miembro_organizacion')->truncate();
        DB::table('celula_miembro')->truncate();
        DB::table('registro_votantes')->truncate();
        DB::table('miembros')->truncate();
        DB::table('familias')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Generar 30 familias
        $apellidos = [
            'Pérez', 'García', 'López', 'González', 'Rodríguez', 
            'Martínez', 'Hernández', 'Gómez', 'Díaz', 'Sánchez', 
            'Álvarez', 'Romero', 'Torres', 'Ruiz', 'Ramírez', 
            'Flores', 'Acosta', 'Ortiz', 'Silva', 'Ramos', 
            'Morales', 'Castillo', 'Guerrero', 'Reyes', 'Mendoza', 
            'Aguilar', 'Jiménez', 'Salazar', 'Vásquez', 'Guzmán'
        ];

        $nombresMasculinos = [
            'Juan', 'Carlos', 'Luis', 'Pedro', 'José', 
            'Miguel', 'Jorge', 'Fernando', 'Ricardo', 'Daniel', 
            'David', 'Manuel', 'Francisco', 'Andrés', 'Javier',
            'Samuel', 'Esteban', 'Mateo', 'Erick', 'Ángel',
            'Alejandro', 'Gabriel', 'Josué', 'Santiago', 'Mauricio',
            'Diego', 'César', 'Hugo', 'Oscar', 'Mario'
        ];

        $nombresFemeninos = [
            'María', 'Ana', 'Luisa', 'Carmen', 'Sofía', 
            'Laura', 'Lucía', 'Elena', 'Isabel', 'Clara', 
            'Marta', 'Sara', 'Paula', 'Andrea', 'Claudia',
            'Débora', 'Rebeca', 'Gabriela', 'Paola', 'Ruth',
            'Raquel', 'Esther', 'Diana', 'Miriam', 'Verónica',
            'Adriana', 'Beatriz', 'Alejandra', 'Valeria', 'Natalia'
        ];

        $ocupaciones = [
            'Perito Contador', 'Secretaria', 'Maestro(a)', 'Ingeniero(a)', 
            'Carpintero', 'Electricista', 'Enfermero(a)', 'Comerciante', 
            'Estudiante', 'Médico', 'Abogado(a)', 'Administrador(a)',
            'Diseñador(a)', 'Constructor', 'Mecánico', 'Chef'
        ];

        $nivelesAcademicos = ['Primaria', 'Básicos', 'Diversificado', 'Universitario', 'Maestría / Postgrado', 'Ninguno'];
        $estadosCiviles = ['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)'];
        $etapasConsolidacion = ['Nuevo', 'En Discipulado', 'Asignado a Célula', 'Bautizado'];

        $familiasIds = [];
        for ($i = 0; $i < 30; $i++) {
            $nombreFamilia = 'Familia ' . $apellidos[$i] . ' ' . $apellidos[($i + 5) % 30];
            $familiasIds[] = DB::table('familias')->insertGetId([
                'nombre' => $nombreFamilia,
                'direccion' => 'Zona ' . (($i % 16) + 1) . ', Calle ' . (($i * 3) % 20 + 1) . ', Avenida ' . (($i * 7) % 15 + 1),
                'telefono_principal' => '4' . str_pad($i * 12345, 7, '0', STR_PAD_LEFT),
                'notas' => 'Familia integrada por asamblea número ' . ($i + 1),
                'celula_id' => null, // Opcional
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Generar 30 miembros y vincularlos
        // Para que se vinculen a las familias, asignaremos 1 miembro a cada una de las 30 familias creadas
        for ($i = 0; $i < 30; $i++) {
            $sexo = ($i % 2 === 0) ? 'M' : 'F';
            $nombre = $sexo === 'M' ? $nombresMasculinos[$i] : $nombresFemeninos[$i];
            $apellido = $apellidos[$i];

            // Generar DPI aleatorio de 13 dígitos
            $cui = '1950' . str_pad($i * 456789, 9, '0', STR_PAD_LEFT);
            $dpi = substr($cui, 0, 13);

            $fechaNacimiento = Carbon::now()->subYears(18 + ($i * 2))->subMonths($i)->subDays($i)->format('Y-m-d');
            $fechaIntegracion = Carbon::now()->subYears($i % 5)->subMonths($i % 12)->subDays($i % 28)->format('Y-m-d');
            
            // Forzar fecha de bautismo para el 80% de los miembros para que puedan ser elegibles para votar
            $fechaBautismo = ($i % 5 !== 0) ? Carbon::now()->subYears($i % 4)->format('Y-m-d') : null;

            // Determinar etapa de consolidación. Si tiene fecha de bautismo, forzar 'Bautizado'
            $etapa = $fechaBautismo ? 'Bautizado' : $etapasConsolidacion[$i % 3];

            $miembroId = DB::table('miembros')->insertGetId([
                'familia_id' => $familiasIds[$i],
                'nombres' => $nombre,
                'apellidos' => $apellido . ' ' . $apellidos[($i + 3) % 30],
                'dpi' => $dpi,
                'fecha_nacimiento' => $fechaNacimiento,
                'sexo' => $sexo,
                'estado_civil' => $estadosCiviles[$i % 4],
                'telefono' => '5' . str_pad($i * 23456, 7, '0', STR_PAD_LEFT),
                'email' => $this->limpiarEmail($nombre, $apellido),
                'direccion' => 'Zona ' . (($i % 16) + 1) . ', Calle ' . (($i * 3) % 20 + 1) . ', Avenida ' . (($i * 7) % 15 + 1),
                'ciudad' => 'Guatemala',
                'nivel_academico' => $nivelesAcademicos[$i % 6],
                'profesion' => $ocupaciones[$i % 16],
                'lugar_trabajo_estudio' => 'Empresa / Institución ' . ($i + 1),
                'estado' => true,
                'foto' => 'default_avatar.png',
                'fecha_integracion' => $fechaIntegracion,
                'fecha_bautismo' => $fechaBautismo,
                'es_lider' => ($i % 5 === 0), // 20% de líderes
                'etapa_consolidacion' => $etapa,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Vincular miembros a algunas organizaciones y ministerios para que no estén vacíos
            $orgIds = DB::table('organizaciones')->pluck('id')->toArray();
            if (!empty($orgIds)) {
                $orgId = $orgIds[$i % count($orgIds)];
                DB::table('miembro_organizacion')->insert([
                    'miembro_id' => $miembroId,
                    'organizacion_id' => $orgId,
                    'puesto' => ($i % 5 === 0) ? 'Líder' : 'Miembro',
                    'fecha_asignacion' => now()->format('Y-m-d'),
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $minIds = DB::table('ministerios')->pluck('id')->toArray();
            if (!empty($minIds)) {
                $minId = $minIds[$i % count($minIds)];
                DB::table('miembro_ministerio')->insert([
                    'miembro_id' => $miembroId,
                    'ministerio_id' => $minId,
                ]);
            }
        }
    }

    private function limpiarEmail(string $nombre, string $apellido): string
    {
        $string = strtolower($nombre . '.' . $apellido);
        $replacements = [
            'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
            'Á'=>'a', 'É'=>'e', 'Í'=>'i', 'Ó'=>'o', 'Ú'=>'u',
            'ñ'=>'n', 'Ñ'=>'n', 'ü'=>'u', 'Ü'=>'u'
        ];
        return strtr($string, $replacements) . '@correo.com';
    }
}
