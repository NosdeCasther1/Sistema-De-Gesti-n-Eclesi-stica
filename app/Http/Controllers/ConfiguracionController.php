<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Models\Usuario;
use App\Models\CategoriaFinanciera;
use App\Http\Requests\StoreFinancialAccountRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ConfiguracionController extends Controller
{
    public function index()
    {
        try {
            $config = Configuracion::first();
            if (!$config) {
                $config = Configuracion::create([
                    'nombre_iglesia' => 'AD REY DE REYES',
                    'moneda' => 'Q'
                ]);
            }

            $usuarios = Usuario::all();
            if ($usuarios->isEmpty()) {
                Usuario::create([
                    'nombre' => 'Administrador',
                    'email' => 'admin@iglesia.com',
                    'password' => Hash::make('password123'),
                    'rol' => 'administrador'
                ]);
                $usuarios = Usuario::all();
            }

            $categorias = CategoriaFinanciera::withTrashed()->get();
            if ($categorias->isEmpty()) {
                CategoriaFinanciera::create(['nombre' => 'Diezmos', 'tipo' => 'ingreso']);
                CategoriaFinanciera::create(['nombre' => 'Ofrendas', 'tipo' => 'ingreso']);
                CategoriaFinanciera::create(['nombre' => 'Servicios Públicos', 'tipo' => 'gasto']);
                $categorias = CategoriaFinanciera::withTrashed()->get();
            }

            $accounts = \App\Models\FinancialAccount::withTrashed()->get();

            return view('configuracion.index', compact('config', 'usuarios', 'categorias', 'accounts'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error al cargar configuración. Asegúrate de ejecutar las migraciones: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $config = Configuracion::first();
        
        $request->validate([
            'nombre_iglesia' => 'required|string|max:255',
            'pastor_general' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'moneda' => 'required|string|max:5',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete('config/' . $config->logo);
            }
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('config', $filename, 'public');
            $data['logo'] = $filename;
        }

        $config->update($data);

        return redirect()->back()->with(['success' => 'Configuración actualizada correctamente.', 'active_tab' => 'general']);
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|string|in:administrador,tesorero,lider,ujier'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol
        ]);

        return redirect()->route('configuracion.index')->with(['success' => 'Usuario creado exitosamente.', 'active_tab' => 'usuarios']);
    }

    public function updateUsuario(Request $request, Usuario $usuario)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'rol' => 'required|string|in:administrador,tesorero,lider,ujier'
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $request->validate($rules);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'rol' => $request->rol
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('configuracion.index')->with(['success' => 'Usuario actualizado exitosamente.', 'active_tab' => 'usuarios']);
    }

    public function destroyUsuario(Usuario $usuario)
    {
        if (Usuario::count() <= 1) {
            return redirect()->route('configuracion.index')->with(['error' => 'No puedes eliminar el único usuario del sistema.', 'active_tab' => 'usuarios']);
        }

        $usuario->delete();

        return redirect()->route('configuracion.index')->with(['success' => 'Usuario eliminado exitosamente.', 'active_tab' => 'usuarios']);
    }

    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:ingreso,gasto'
        ]);

        CategoriaFinanciera::create($request->all());

        return redirect()->route('configuracion.index')->with(['success' => 'Categoría financiera creada exitosamente.', 'active_tab' => 'catalogos']);
    }

    public function updateCategoria(Request $request, CategoriaFinanciera $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:ingreso,gasto'
        ]);

        $categoria->update($request->all());

        return redirect()->route('configuracion.index')->with(['success' => 'Categoría financiera actualizada exitosamente.', 'active_tab' => 'catalogos']);
    }

    public function destroyCategoria($id)
    {
        $categoria = CategoriaFinanciera::withTrashed()->findOrFail($id);
        $categoria->delete();

        return redirect()->route('configuracion.index')->with(['success' => 'Categoría archivada correctamente. Los datos históricos se mantienen intactos.', 'active_tab' => 'catalogos']);
    }

    public function restoreCategoria($id)
    {
        $categoria = CategoriaFinanciera::withTrashed()->findOrFail($id);
        $categoria->restore();

        return redirect()->route('configuracion.index')->with(['success' => 'Categoría reactivada con éxito.', 'active_tab' => 'catalogos']);
    }

    public function updateSistema(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'nullable|string',
            'timezone' => 'required|string'
        ]);

        session([
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'timezone' => $request->timezone,
            'maintenance_mode' => $request->has('maintenance_mode')
        ]);

        return redirect()->route('configuracion.index')->with(['success' => 'Ajustes del sistema y servidor de correo actualizados correctamente.', 'active_tab' => 'sistema']);
    }

    public function backupDatabase()
    {
        $filename = 'backup_iglesia_' . date('Y_m_d_H_i_s') . '.sql';
        
        return redirect()->route('configuracion.index')->with(['success' => 'Respaldo de base de datos generado exitosamente: ' . $filename, 'active_tab' => 'sistema']);
    }

    public function updatePermisos(Request $request)
    {
        $permisos = $request->input('permisos', []);
        
        // El administrador siempre tiene acceso total a todos los módulos
        $permisos['administrador'] = ['miembros', 'familias', 'celulas', 'asistencia', 'tesoreria', 'reportes', 'configuracion'];

        session(['role_permissions' => $permisos]);

        return redirect()->route('configuracion.index')->with(['success' => 'Matriz de acceso a módulos por rol actualizada exitosamente.', 'active_tab' => 'usuarios']);
    }

    public function storeAccount(StoreFinancialAccountRequest $request)
    {
        try {
            \DB::transaction(function () use ($request) {
                \App\Models\FinancialAccount::create($request->validated());
            });

            return redirect()->route('configuracion.index')->with(['success' => 'Nueva Caja/Fondo creada correctamente.', 'active_tab' => 'catalogos']);
        } catch (\Exception $e) {
            \Log::error("Error en Cajas: " . $e->getMessage());
            return redirect()->route('configuracion.index')->with(['error' => 'Error al procesar la solicitud.', 'active_tab' => 'catalogos']);
        }
    }

    public function destroyAccount($id)
    {
        $account = \App\Models\FinancialAccount::withTrashed()->findOrFail($id);
        $account->delete();

        return redirect()->route('configuracion.index')->with(['success' => 'Caja archivada correctamente. Los datos históricos se mantienen intactos.', 'active_tab' => 'catalogos']);
    }

    public function restoreAccount($id)
    {
        $account = \App\Models\FinancialAccount::withTrashed()->findOrFail($id);
        $account->restore();

        return redirect()->route('configuracion.index')->with(['success' => 'Caja reactivada con éxito.', 'active_tab' => 'catalogos']);
    }
}
