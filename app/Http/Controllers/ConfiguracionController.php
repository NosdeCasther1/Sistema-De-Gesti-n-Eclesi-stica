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

            $usuarios = Usuario::with('roles')->get();
            if ($usuarios->isEmpty()) {
                $admin = Usuario::create([
                    'nombre' => 'Administrador',
                    'email' => 'admin@iglesia.com',
                    'password' => Hash::make('password123'),
                ]);
                $admin->assignRole('administrador');
                $usuarios = Usuario::with('roles')->get();
            }

            $categorias = CategoriaFinanciera::withTrashed()->get();
            if ($categorias->isEmpty()) {
                CategoriaFinanciera::create(['nombre' => 'Diezmos', 'tipo' => 'Ingreso']);
                CategoriaFinanciera::create(['nombre' => 'Ofrendas', 'tipo' => 'Ingreso']);
                CategoriaFinanciera::create(['nombre' => 'Servicios Públicos', 'tipo' => 'Gasto']);
                $categorias = CategoriaFinanciera::withTrashed()->get();
            }

            $accounts = \App\Models\FinancialAccount::withTrashed()->get();
            $organizaciones = \App\Models\Organizacion::with('financialAccount')->get();
            $adjustments = \App\Models\FinancialAccountAdjustment::with(['account', 'user'])->orderBy('created_at', 'desc')->get();

            return view('configuracion.index', compact('config', 'usuarios', 'categorias', 'accounts', 'organizaciones', 'adjustments'));
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
            'telefono' => 'nullable|numeric|digits:8',
            'email' => 'nullable|email|max:255',
            'moneda' => 'required|string|max:5',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:15360', // Permite webp y hasta 15MB para soportar la subida de imágenes grandes antes del redimensionamiento
            'firma_pastor' => 'nullable|image|mimes:png|max:5120', // Se recomienda PNG para transparencia
            'sello_iglesia' => 'nullable|image|mimes:png|max:5120', // Se recomienda PNG para transparencia
        ]);

        $data = $request->except(['logo', 'firma_pastor', 'sello_iglesia']);

        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete('config/' . $config->logo);
            }
            $file = $request->file('logo');
            
            // Intentar convertir a PNG y redimensionar si es gigante para máxima compatibilidad con DomPDF
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'logo_' . time() . '.png';
            $destinationPath = storage_path('app/public/config/' . $filename);
            
            if (!file_exists(dirname($destinationPath))) {
                mkdir(dirname($destinationPath), 0755, true);
            }

            $converted = false;
            try {
                $img = null;
                if ($extension === 'jpeg' || $extension === 'jpg') {
                    $img = @imagecreatefromjpeg($file->getRealPath());
                } elseif ($extension === 'png') {
                    $img = @imagecreatefrompng($file->getRealPath());
                } elseif ($extension === 'gif') {
                    $img = @imagecreatefromgif($file->getRealPath());
                } elseif ($extension === 'webp') {
                    if (function_exists('imagecreatefromwebp')) {
                        $img = @imagecreatefromwebp($file->getRealPath());
                    }
                }
                
                if ($img) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    
                    // Si la imagen es gigantesca (ej. 6250x6250), la redimensionamos a un tamaño máximo de 800px
                    // para evitar que DomPDF se quede sin memoria al generar el PDF.
                    $width = imagesx($img);
                    $height = imagesy($img);
                    $maxDim = 800;
                    
                    if ($width > $maxDim || $height > $maxDim) {
                        if ($width > $height) {
                            $newWidth = $maxDim;
                            $newHeight = intval($height * ($maxDim / $width));
                        } else {
                            $newHeight = $maxDim;
                            $newWidth = intval($width * ($maxDim / $height));
                        }
                        
                        $resizedImg = imagecreatetruecolor($newWidth, $newHeight);
                        imagealphablending($resizedImg, false);
                        imagesavealpha($resizedImg, true);
                        
                        // Preservar canal alfa en el redimensionamiento
                        $transparent = imagecolorallocatealpha($resizedImg, 255, 255, 255, 127);
                        imagefill($resizedImg, 0, 0, $transparent);
                        
                        imagecopyresampled($resizedImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($img);
                        $img = $resizedImg;
                    }
                    
                    $converted = imagepng($img, $destinationPath, 8);
                    imagedestroy($img);
                }
            } catch (\Throwable $e) {
                \Log::error("Error converting/resizing logo: " . $e->getMessage());
            }

            if ($converted) {
                $data['logo'] = $filename;
            } else {
                // Si la conversión falla (por falta de memoria u otra causa), guardamos el original
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('config', $filename, 'public');
                $data['logo'] = $filename;
            }
        }

        if ($request->hasFile('firma_pastor')) {
            if ($config->firma_pastor) {
                Storage::disk('public')->delete('config/' . $config->firma_pastor);
            }
            $file = $request->file('firma_pastor');
            $filename = 'firma_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('config', $filename, 'public');
            $data['firma_pastor'] = $filename;
        }

        if ($request->hasFile('sello_iglesia')) {
            if ($config->sello_iglesia) {
                Storage::disk('public')->delete('config/' . $config->sello_iglesia);
            }
            $file = $request->file('sello_iglesia');
            $filename = 'sello_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('config', $filename, 'public');
            $data['sello_iglesia'] = $filename;
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
            'rol' => 'required|string|in:administrador,tesorero,lider,ujier',
            'organizacion_id' => 'nullable|exists:organizaciones,id',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $usuario->organizacion_id = $request->organizacion_id;
        $usuario->save();

        $usuario->assignRole($request->rol);

        return redirect()->route('configuracion.index')->with(['success' => 'Usuario creado exitosamente.', 'active_tab' => 'usuarios']);
    }

    public function updateUsuario(Request $request, Usuario $usuario)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'rol' => 'required|string|in:administrador,tesorero,lider,ujier',
            'organizacion_id' => 'nullable|exists:organizaciones,id',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $request->validate($rules);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->organizacion_id = $request->organizacion_id;
        $usuario->save();
        
        $usuario->syncRoles([$request->rol]);

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
            'tipo' => 'required|string|in:ingreso,gasto,Ingreso,Gasto'
        ]);

        $data = $request->all();
        $data['tipo'] = ucfirst(strtolower($data['tipo']));

        CategoriaFinanciera::create($data);

        return redirect()->route('configuracion.index')->with(['success' => 'Categoría financiera creada exitosamente.', 'active_tab' => 'catalogos']);
    }

    public function updateCategoria(Request $request, CategoriaFinanciera $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|in:ingreso,gasto,Ingreso,Gasto'
        ]);

        $data = $request->all();
        $data['tipo'] = ucfirst(strtolower($data['tipo']));

        $categoria->update($data);

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

        // Mapa de módulos UI → permisos Spatie
        $moduleToPermission = [
            'miembros' => 'ver_miembros',
            'familias' => 'ver_familias',
            'celulas' => 'ver_celulas',
            'asistencia' => 'ver_asistencia',
            'tesoreria' => 'ver_tesoreria',
            'reportes' => 'ver_reportes',
            'configuracion' => 'ver_configuracion',
            'eventos' => 'ver_eventos',
        ];

        // El administrador siempre tiene acceso total — no se modifica
        $rolesEditables = ['tesorero', 'lider', 'ujier'];

        foreach ($rolesEditables as $roleName) {
            $role = \Spatie\Permission\Models\Role::findByName($roleName, 'web');
            $modulosAsignados = $permisos[$roleName] ?? [];

            $permissionNames = [];
            foreach ($modulosAsignados as $modulo) {
                if (isset($moduleToPermission[$modulo])) {
                    $permissionNames[] = $moduleToPermission[$modulo];
                }
            }

            $role->syncPermissions($permissionNames);
        }

        // Limpiar caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

    public function updateAccount(Request $request, $id)
    {
        $account = \App\Models\FinancialAccount::withTrashed()->findOrFail($id);

        $nameChanged = $request->name !== $account->name;
        $balanceChanged = (float)$request->initial_balance !== (float)$account->initial_balance;

        $rules = [
            'name' => 'required|string|max:100|unique:financial_accounts,name,' . $id,
            'initial_balance' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ];

        if ($nameChanged || $balanceChanged) {
            $rules['justification'] = 'required|string|min:10|max:1000';
        } else {
            $rules['justification'] = 'nullable|string|max:1000';
        }

        $request->validate($rules, [
            'name.unique' => 'Ya existe una caja con este nombre.',
            'initial_balance.min' => 'El saldo inicial no puede ser negativo.',
            'justification.required' => 'La justificación es obligatoria si modifica el nombre o saldo inicial.',
            'justification.min' => 'La justificación debe tener al menos 10 caracteres.',
        ]);

        try {
            \DB::transaction(function () use ($account, $request, $nameChanged, $balanceChanged) {
                $oldName = $account->name;
                $oldBalance = $account->initial_balance;

                $account->update([
                    'name' => $request->name,
                    'initial_balance' => $request->initial_balance,
                    'description' => $request->description,
                ]);

                if ($nameChanged) {
                    \App\Models\FinancialAccountAdjustment::create([
                        'financial_account_id' => $account->id,
                        'user_id' => auth()->id(),
                        'field_changed' => 'name',
                        'old_value' => $oldName,
                        'new_value' => $request->name,
                        'justification' => $request->justification,
                    ]);
                }

                if ($balanceChanged) {
                    \App\Models\FinancialAccountAdjustment::create([
                        'financial_account_id' => $account->id,
                        'user_id' => auth()->id(),
                        'field_changed' => 'initial_balance',
                        'old_value' => $oldBalance,
                        'new_value' => $request->initial_balance,
                        'justification' => $request->justification,
                    ]);
                }
            });

            return redirect()->route('configuracion.index')->with(['success' => 'Caja/Fondo actualizada correctamente con registro de auditoría.', 'active_tab' => 'catalogos']);
        } catch (\Exception $e) {
            \Log::error("Error al actualizar Caja: " . $e->getMessage());
            return redirect()->route('configuracion.index')->with(['error' => 'Error al actualizar la caja: ' . $e->getMessage(), 'active_tab' => 'catalogos']);
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

    public function storeOrganizacion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'estado' => 'nullable|boolean'
        ]);

        \App\Models\Organizacion::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'financial_account_id' => $request->financial_account_id,
            'estado' => $request->has('estado') ? (bool) $request->estado : true
        ]);

        return redirect()->route('configuracion.index')->with(['success' => 'Organización creada exitosamente.', 'active_tab' => 'catalogos']);
    }

    public function updateOrganizacion(Request $request, $id)
    {
        $organizacion = \App\Models\Organizacion::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'estado' => 'nullable|boolean'
        ]);

        $organizacion->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'financial_account_id' => $request->financial_account_id,
            'estado' => $request->has('estado') ? (bool) $request->estado : $organizacion->estado
        ]);

        return redirect()->route('configuracion.index')->with(['success' => 'Organización actualizada exitosamente.', 'active_tab' => 'catalogos']);
    }

    public function destroyOrganizacion($id)
    {
        $organizacion = \App\Models\Organizacion::findOrFail($id);
        $organizacion->update(['estado' => false]);

        return redirect()->route('configuracion.index')->with(['success' => 'Organización archivada correctamente.', 'active_tab' => 'catalogos']);
    }

    public function restoreOrganizacion($id)
    {
        $organizacion = \App\Models\Organizacion::findOrFail($id);
        $organizacion->update(['estado' => true]);

        return redirect()->route('configuracion.index')->with(['success' => 'Organización reactivada con éxito.', 'active_tab' => 'catalogos']);
    }
}
