<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Familia;
use App\Models\Miembro;

class MigrateLegacyData extends Command
{
    protected $signature = 'iglesia:migrate-legacy';
    protected $description = 'Migra datos reales del MVC anterior a la estructura Eloquent de Laravel presenvando IDs y relaciones correctas';

    public function handle()
    {
        $this->info('Iniciando proceso de migración ETL...');

        Schema::disableForeignKeyConstraints();
        
        Familia::truncate();
        Miembro::truncate();

        $this->info('Migrando Familias...');
        try {
            $totalFamilias = DB::connection('legacy')->table('familias')->count();
            $bar = $this->output->createProgressBar($totalFamilias);

            DB::connection('legacy')->table('familias')->orderBy('id')->chunk(100, function ($familiasLegacy) use ($bar) {
                foreach ($familiasLegacy as $f) {
                    DB::table('familias')->insert([
                        'id' => $f->id,
                        'nombre' => $f->nombre,
                        'direccion' => null,
                        'telefono_principal' => null,
                        'notas' => $f->descripcion ?? null,
                        'created_at' => $f->fecha_creacion ?? now(),
                        'updated_at' => $f->fecha_actualizacion ?? now(),
                    ]);
                    $bar->advance();
                }
            });
            $bar->finish();
            $this->newLine(2);
        } catch (\Exception $e) {
            $this->error('Error al migrar familias: ' . $e->getMessage());
        }

        $this->info('Migrando Miembros...');
        try {
            $totalMiembros = DB::connection('legacy')->table('miembros')->count();
            $bar = $this->output->createProgressBar($totalMiembros);

            DB::connection('legacy')->table('miembros')->orderBy('miembro_id')->chunk(100, function ($miembrosLegacy) use ($bar) {
                foreach ($miembrosLegacy as $m) {
                    $familiaId = $m->familia ? (int)$m->familia : null;
                    
                    // Si el ID de familia no existe en la tabla de familias, lo creamos para mantener integridad referencial
                    if ($familiaId && !DB::table('familias')->where('id', $familiaId)->exists()) {
                        // Usar el apellido del primer miembro para darle un nombre de familia real y bonito
                        $nombreFamilia = 'Familia ' . ($m->apellidos ?: 'Grupo ' . $familiaId);
                        DB::table('familias')->insert([
                            'id' => $familiaId,
                            'nombre' => $nombreFamilia,
                            'direccion' => $m->direccion ?? null,
                            'telefono_principal' => $m->tel_celular ?? null,
                            'notas' => 'Familia creada automáticamente durante la migración.',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    DB::table('miembros')->insert([
                        'id' => $m->miembro_id,
                        'familia_id' => $familiaId,
                        'nombres' => $m->nombres,
                        'apellidos' => $m->apellidos,
                        'dpi' => $m->no_dpi ?? null,
                        'fecha_nacimiento' => $m->fecha_nacimiento,
                        'sexo' => $m->sexo,
                        'estado_civil' => $m->estado_civil,
                        'telefono' => $m->tel_celular ?? $m->tel_fijo ?? null,
                        'email' => $m->email,
                        'direccion' => $m->direccion,
                        'ciudad' => $m->ciudad,
                        'ministerio' => $m->cargo ?? null,
                        'estado' => ($m->estado ?? 'Activo') === 'Activo' ? 1 : 0,
                        'foto' => $m->foto ?: 'default_avatar.png',
                        'fecha_integracion' => $m->fecha_ingreso,
                        'etapa_consolidacion' => 'Nuevo',
                        'created_at' => $m->fecha_creacion ?? now(),
                        'updated_at' => $m->fecha_actualizacion ?? now(),
                    ]);
                    $bar->advance();
                }
            });
            $bar->finish();
        } catch (\Exception $e) {
            $this->error('Error al migrar miembros: ' . $e->getMessage());
        }

        Schema::enableForeignKeyConstraints();

        $this->newLine(2);
        $this->info("✅ Migración completada.");
    }
}
