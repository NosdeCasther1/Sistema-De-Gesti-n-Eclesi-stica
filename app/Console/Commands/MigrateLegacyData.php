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
    protected $description = 'Migra datos reales del MVC anterior a la estructura Eloquent de Laravel';

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
                    Familia::create([
                        'id' => $f->id,
                        'nombre' => $f->nombre,
                        'direccion' => null, // No existe en tabla vieja
                        'telefono_principal' => null, // No existe en tabla vieja
                        'notas' => $f->descripcion ?? null,
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
            // Mapeo de nombres de familias a IDs para la relación
            $familiasMap = Familia::pluck('id', 'nombre')->toArray();

            $totalMiembros = DB::connection('legacy')->table('miembros')->count();
            $bar = $this->output->createProgressBar($totalMiembros);

            DB::connection('legacy')->table('miembros')->orderBy('miembro_id')->chunk(100, function ($miembrosLegacy) use ($bar, $familiasMap) {
                foreach ($miembrosLegacy as $m) {
                    // Buscar el ID de la familia por nombre
                    $familiaId = $familiasMap[$m->familia] ?? null;
                    
                    // Si no existe la familia, crearla o asignar una por defecto
                    if (!$familiaId && !empty($m->familia)) {
                        $nuevaFamilia = Familia::create(['nombre' => $m->familia]);
                        $familiasMap[$m->familia] = $nuevaFamilia->id;
                        $familiaId = $nuevaFamilia->id;
                    }

                    Miembro::create([
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
                        'estado' => ($m->estado ?? 'Activo') === 'Activo',
                        'foto' => $m->foto ?: 'default_avatar.png',
                        'fecha_integracion' => $m->fecha_ingreso,
                        'etapa_consolidacion' => 'Nuevo', // Valor por defecto
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
