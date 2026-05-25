<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Usuario;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permisos por Módulo ───
        $permissions = [
            'ver_miembros',
            'ver_familias',
            'ver_celulas',
            'ver_eventos',
            'ver_asistencia',
            'ver_tesoreria',
            'ver_reportes',
            'ver_configuracion',
            'gestionar_elecciones',
            'gestionar_organizaciones',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ─── Roles y sus permisos por defecto ───
        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions); // Acceso total

        $tesoreroRole = Role::firstOrCreate(['name' => 'tesorero', 'guard_name' => 'web']);
        $tesoreroRole->syncPermissions([
            'ver_miembros',
            'ver_familias',
            'ver_eventos',
            'ver_asistencia',
            'ver_tesoreria',
            'ver_reportes',
        ]);

        $liderRole = Role::firstOrCreate(['name' => 'lider', 'guard_name' => 'web']);
        $liderRole->syncPermissions([
            'ver_miembros',
            'ver_familias',
            'ver_celulas',
            'ver_eventos',
            'ver_asistencia',
        ]);

        $ujierRole = Role::firstOrCreate(['name' => 'ujier', 'guard_name' => 'web']);
        $ujierRole->syncPermissions([
            'ver_asistencia',
        ]);

        // ─── Migrar usuarios existentes de columna 'rol' a Spatie roles ───
        $usuarios = Usuario::all();
        foreach ($usuarios as $usuario) {
            // Leer la columna legacy 'rol' si aún existe
            $legacyRol = $usuario->getRawOriginal('rol') ?? 'ujier';
            $roleName = in_array($legacyRol, ['administrador', 'tesorero', 'lider', 'ujier'])
                ? $legacyRol
                : 'ujier';

            if ($usuario->roles->isEmpty()) {
                $usuario->assignRole($roleName);
            }
        }

        // ─── Crear usuario admin por defecto si no existe ninguno ───
        if (Usuario::count() === 0) {
            $admin = Usuario::create([
                'nombre' => 'Administrador',
                'email' => 'admin@iglesia.com',
                'password' => bcrypt('password123'),
            ]);
            $admin->assignRole('administrador');
        }
    }
}
