<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\VotoController;
use App\Http\Controllers\OrganizacionController;
use App\Http\Controllers\EleccionController;
use App\Http\Controllers\PortalVotanteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/check-session', function () {
        return response()->json(['status' => 'active']);
    })->name('check.session');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/Inicio', [DashboardController::class, 'index']); // Alias de URL (compatibilidad con marcadores antiguos)

    Route::get('/miembros/{miembro}/carnet', [MiembroController::class, 'generarCarnet'])->name('miembros.carnet');
    Route::get('/miembros/{miembro}/carta-recomendacion', [MiembroController::class, 'cartaRecomendacion'])->name('miembros.carta_recomendacion');
    Route::get('/miembros/{miembro}/carta-traslado', [MiembroController::class, 'cartaTraslado'])->name('miembros.carta_traslado');
    Route::get('/miembros/{miembro}/certificado-bautismo', [MiembroController::class, 'certificadoBautismo'])->name('miembros.certificado_bautismo');
Route::resource('familias', FamiliaController::class);
Route::resource('miembros', MiembroController::class);

// Certificados
Route::resource('certificados/presentacion', App\Http\Controllers\CertificadoPresentacionController::class)->names('presentacion');
Route::get('certificados/presentacion/{presentacion}/pdf', [App\Http\Controllers\CertificadoPresentacionController::class, 'pdf'])->name('presentacion.pdf');
Route::resource('certificados/matrimonio', App\Http\Controllers\CertificadoMatrimonioController::class)->names('matrimonio');
Route::get('certificados/matrimonio/{matrimonio}/pdf', [App\Http\Controllers\CertificadoMatrimonioController::class, 'pdf'])->name('matrimonio.pdf');
Route::resource('inventario', \App\Http\Controllers\InventarioController::class);
    Route::post('/miembros/{miembro}/records', [App\Http\Controllers\MiembroController::class, 'storeRecord'])->name('miembros.records.store');
    Route::delete('/records/{record}', [App\Http\Controllers\MiembroController::class, 'destroyRecord'])->name('miembros.records.destroy');

    Route::resource('familias', App\Http\Controllers\FamiliaController::class);
Route::resource('celulas', \App\Http\Controllers\CelulaController::class);
Route::post('/tesoreria/transfer', [\App\Http\Controllers\TesoreriaController::class, 'transfer'])->name('tesoreria.transfer')->middleware('role:tesorero|administrador');
Route::resource('tesoreria', \App\Http\Controllers\TesoreriaController::class)->middleware('role:tesorero|administrador');
Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
Route::get('/reportes/tesoreria', [\App\Http\Controllers\ReporteController::class, 'reportarTesoreria'])->name('reportes.tesoreria');
Route::get('/reportes/tesoreria/pdf', [\App\Http\Controllers\ReporteTesoreriaController::class, 'generateCorteCaja'])->name('reportes.tesoreria.pdf');
Route::get('/reportes/membresia', [\App\Http\Controllers\ReporteController::class, 'reportarMembresiaDinamico'])->name('reportes.membresia');
Route::get('/reportes/inventario', [\App\Http\Controllers\ReporteController::class, 'reportarInventario'])->name('reportes.inventario');
Route::get('/reportes/organizaciones', [\App\Http\Controllers\ReporteController::class, 'reportarOrganizaciones'])->name('reportes.organizaciones');
Route::get('/reportes/ingresos-familia', [\App\Http\Controllers\ReporteController::class, 'reportarIngresosFamilia'])->name('reportes.ingresos_familia');
Route::get('/reportes/asistencia/celula/{id}', [\App\Http\Controllers\ReporteController::class, 'reportarAsistenciaCelula'])->name('reportes.asistencia_celula');
Route::get('/reportes/asistencia/evento/{id}', [\App\Http\Controllers\ReporteController::class, 'reportarAsistenciaEvento'])->name('reportes.asistencia_evento');

// Eventos y Google Calendar
Route::get('/eventos-calendar', [\App\Http\Controllers\EventoController::class, 'calendarEvents'])->name('eventos.calendar');
Route::resource('eventos', \App\Http\Controllers\EventoController::class);

Route::get('/google-calendar/connect', [\App\Http\Controllers\GoogleCalendarController::class, 'connect'])->name('google.calendar.connect');
Route::get('/google-calendar/callback', [\App\Http\Controllers\GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');
Route::post('/google-calendar/disconnect', [\App\Http\Controllers\GoogleCalendarController::class, 'disconnect'])->name('google.calendar.disconnect');
Route::post('/google-calendar/select', [\App\Http\Controllers\GoogleCalendarController::class, 'selectCalendar'])->name('google.calendar.select');
Route::post('/google-calendar/sync', [\App\Http\Controllers\GoogleCalendarController::class, 'sync'])->name('google.calendar.sync');


// Asistencia QR
Route::get('/asistencia/scanner', [\App\Http\Controllers\AsistenciaController::class, 'scanner'])->name('asistencia.scanner');
Route::get('/asistencia/manual', [\App\Http\Controllers\AsistenciaController::class, 'create'])->name('asistencia.manual');
Route::post('/asistencia/registrar', [\App\Http\Controllers\AsistenciaController::class, 'registrar'])->name('asistencia.registrar');

// Simulador de Rol (RBAC) — Solo en entorno local
Route::get('/switch-role/{rol}', function ($rol) {
    if (!app()->isLocal()) {
        abort(403, 'Simulación de roles deshabilitada en producción.');
    }
    if (in_array($rol, ['administrador', 'tesorero', 'lider', 'ujier'])) {
        $user = auth()->user() ?? \App\Models\Usuario::first();
        if ($user) {
            $user->syncRoles([$rol]);
        }
    }
    return redirect()->route('dashboard')->with('success', 'Nivel de acceso cambiado a: ' . strtoupper($rol));
})->name('switch.role');

// Configuración (Solo Administradores)
Route::get('/configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index')->middleware('role:administrador');
Route::post('/configuracion/update', [\App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update')->middleware('role:administrador');

// Usuarios CRUD (dentro de Configuración - Solo Administradores)
Route::post('/configuracion/usuarios', [\App\Http\Controllers\ConfiguracionController::class, 'storeUsuario'])->name('usuarios.store')->middleware('role:administrador');
Route::put('/configuracion/usuarios/{usuario}', [\App\Http\Controllers\ConfiguracionController::class, 'updateUsuario'])->name('usuarios.update')->middleware('role:administrador');
Route::delete('/configuracion/usuarios/{usuario}', [\App\Http\Controllers\ConfiguracionController::class, 'destroyUsuario'])->name('usuarios.destroy')->middleware('role:administrador');

// Catálogos CRUD (dentro de Configuración - Solo Administradores)
Route::post('/configuracion/categorias', [\App\Http\Controllers\ConfiguracionController::class, 'storeCategoria'])->name('categorias.store')->middleware('role:administrador');
Route::put('/configuracion/categorias/{categoria}', [\App\Http\Controllers\ConfiguracionController::class, 'updateCategoria'])->name('categorias.update')->middleware('role:administrador');
Route::delete('/configuracion/categorias/{categoria}', [\App\Http\Controllers\ConfiguracionController::class, 'destroyCategoria'])->name('categorias.destroy')->middleware('role:administrador');
Route::post('/configuracion/categorias/{id}/restore', [\App\Http\Controllers\ConfiguracionController::class, 'restoreCategoria'])->name('categorias.restore')->middleware('role:administrador');

Route::post('/configuracion/accounts', [\App\Http\Controllers\ConfiguracionController::class, 'storeAccount'])->name('configuracion.accounts.store')->middleware('role:administrador');
Route::put('/configuracion/accounts/{id}', [\App\Http\Controllers\ConfiguracionController::class, 'updateAccount'])->name('configuracion.accounts.update')->middleware('role:administrador');
Route::delete('/configuracion/accounts/{id}', [\App\Http\Controllers\ConfiguracionController::class, 'destroyAccount'])->name('configuracion.accounts.destroy')->middleware('role:administrador');
Route::post('/configuracion/accounts/{id}/restore', [\App\Http\Controllers\ConfiguracionController::class, 'restoreAccount'])->name('configuracion.accounts.restore')->middleware('role:administrador');

// Organizaciones CRUD (dentro de Configuración - Solo Administradores)
Route::post('/configuracion/organizaciones', [\App\Http\Controllers\ConfiguracionController::class, 'storeOrganizacion'])->name('configuracion.organizaciones.store')->middleware('role:administrador');
Route::put('/configuracion/organizaciones/{id}', [\App\Http\Controllers\ConfiguracionController::class, 'updateOrganizacion'])->name('configuracion.organizaciones.update')->middleware('role:administrador');
Route::delete('/configuracion/organizaciones/{id}', [\App\Http\Controllers\ConfiguracionController::class, 'destroyOrganizacion'])->name('configuracion.organizaciones.destroy')->middleware('role:administrador');
Route::post('/configuracion/organizaciones/{id}/restore', [\App\Http\Controllers\ConfiguracionController::class, 'restoreOrganizacion'])->name('configuracion.organizaciones.restore')->middleware('role:administrador');

// Sistema y Mantenimiento (dentro de Configuración - Solo Administradores)
Route::post('/configuracion/sistema', [\App\Http\Controllers\ConfiguracionController::class, 'updateSistema'])->name('sistema.update')->middleware('role:administrador');
Route::post('/configuracion/backup', [\App\Http\Controllers\ConfiguracionController::class, 'backupDatabase'])->name('sistema.backup')->middleware('role:administrador');
Route::post('/configuracion/permisos', [\App\Http\Controllers\ConfiguracionController::class, 'updatePermisos'])->name('permisos.update')->middleware('role:administrador');

// Módulo de Organizaciones y Votaciones
    // Vista Principal Bento UI
    Route::get('/organizaciones', [OrganizacionController::class, 'index'])->name('organizaciones.index');
    
    // Padrón de Organización (Solo Admins)
    Route::post('/organizaciones/{organizacion}/sync-miembros', [OrganizacionController::class, 'syncMiembros'])
        ->name('organizaciones.sync-miembros');
        
    // Emisión de Voto (Autoservicio y Asistido)
    Route::post('/votos/emitir', [VotoController::class, 'store'])->name('votos.store');
    
    // Ciclo de Vida de la Elección
    Route::patch('/elecciones/{eleccion}/estado', [EleccionController::class, 'cambiarEstado'])->name('elecciones.estado');
    Route::post('/elecciones/{eleccion}/sync-candidatos', [EleccionController::class, 'syncCandidatos'])->name('elecciones.sync-candidatos');
    Route::get('/elecciones/{eleccion}/kiosco', [EleccionController::class, 'kiosco'])->name('elecciones.kiosco');
    Route::get('elecciones/{eleccion}/reporte', [\App\Http\Controllers\EleccionController::class, 'reporteEscrutinio'])->name('elecciones.reporte');

    Route::get('organizaciones/{organizacion}/reporte-miembros', [\App\Http\Controllers\OrganizacionController::class, 'reporteMiembros'])->name('organizaciones.reporte_miembros');

// Comunicaciones
Route::get('comunicaciones/whatsapp', [\App\Http\Controllers\WhatsappController::class, 'index'])->name('comunicaciones.whatsapp.index');

// Dashboard and related routes
    Route::get('/elecciones/{eleccion}/live', [App\Http\Controllers\EleccionController::class, 'liveScreen'])->name('elecciones.live');
    Route::get('/elecciones/{eleccion}/live-data', [App\Http\Controllers\EleccionController::class, 'liveData'])->name('elecciones.live.data');
    Route::get('/elecciones/{eleccion}/pin', [App\Http\Controllers\ProyectorController::class, 'pinScreen'])->name('elecciones.proyector.pin');
    
    // Reportes de Votaciones y Elecciones
    Route::get('/reportes/votaciones/{eleccion}/escrutinio', [\App\Http\Controllers\ReporteController::class, 'reportarVotacionesEscrutinio'])->name('reportes.votaciones.escrutinio');
    Route::get('/reportes/votaciones/{eleccion}/participantes', [\App\Http\Controllers\ReporteController::class, 'reportarVotacionesParticipantes'])->name('reportes.votaciones.participantes');
    Route::get('/reportes/votaciones/{eleccion}/conformacion', [\App\Http\Controllers\ReporteController::class, 'reportarVotacionesConformacion'])->name('reportes.votaciones.conformacion');
    
    // Iniciar nueva elección en una organización
    Route::post('/organizaciones/{organizacion}/iniciar-eleccion', [OrganizacionController::class, 'iniciarEleccion'])->name('organizaciones.iniciar-eleccion');

    // --- PORTAL DEL VOTANTE (MIEMBROS) ---
    Route::get('/votar', [PortalVotanteController::class, 'index'])->name('votar.index');
    Route::post('/votar/acceder', [PortalVotanteController::class, 'validarPin'])->name('votar.acceder');
    Route::get('/votar/identificar', [PortalVotanteController::class, 'identificar'])->name('votar.identificar');
    Route::post('/votar/identificar', [PortalVotanteController::class, 'procesarIdentificacion'])->name('votar.procesar-identificacion');
    Route::get('/votar/papeleta', [PortalVotanteController::class, 'papeleta'])->name('votar.papeleta');
    Route::post('/votar/cambiar-votante', [PortalVotanteController::class, 'cambiarVotante'])->name('votar.cambiar-votante');
    Route::post('/votar/salir', [PortalVotanteController::class, 'salirPortal'])->name('votar.salir');
    Route::get('/votar/buscar-miembro', [PortalVotanteController::class, 'buscarMiembro'])->name('votar.buscar-miembro');

    // --- GESTIÓN DE RONDAS (ADMIN) ---
    Route::patch('/elecciones/{eleccion}/ronda/abrir', [App\Http\Controllers\EleccionController::class, 'abrirRonda'])->name('elecciones.ronda.abrir');
    Route::patch('/elecciones/{eleccion}/ronda/cerrar', [App\Http\Controllers\EleccionController::class, 'cerrarRonda'])->name('elecciones.ronda.cerrar');
    Route::patch('/elecciones/{eleccion}/ronda/regenerar-pin', [App\Http\Controllers\EleccionController::class, 'regenerarPin'])->name('elecciones.ronda.regenerar-pin');
    Route::post('/elecciones/{eleccion}/ronda/manuales', [App\Http\Controllers\EleccionController::class, 'registrarVotosManuales'])->name('elecciones.ronda.manuales');

    // --- MI PERFIL Y SEGURIDAD ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/logout-other-devices', [ProfileController::class, 'logoutOtherDevices'])->name('profile.logoutOtherDevices');
    Route::get('/acerca-de', function () {
        return view('acerca');
    })->name('acerca');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
