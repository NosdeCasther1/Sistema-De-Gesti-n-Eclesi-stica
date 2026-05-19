<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\MiembroController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/Inicio', [DashboardController::class, 'index']); // For legacy compatibility

Route::get('/miembros/{miembro}/carnet', [MiembroController::class, 'generarCarnet'])->name('miembros.carnet');
Route::resource('familias', FamiliaController::class);
Route::resource('miembros', MiembroController::class);
Route::resource('celulas', \App\Http\Controllers\CelulaController::class);
Route::post('/tesoreria/transfer', [\App\Http\Controllers\TesoreriaController::class, 'transfer'])->name('tesoreria.transfer')->middleware('role:tesorero');
Route::resource('tesoreria', \App\Http\Controllers\TesoreriaController::class)->middleware('role:tesorero');
Route::get('/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
Route::get('/reportes/tesoreria', [\App\Http\Controllers\ReporteController::class, 'reportarTesoreria'])->name('reportes.tesoreria');
Route::get('/reportes/tesoreria/pdf', [\App\Http\Controllers\ReporteTesoreriaController::class, 'generateCorteCaja'])->name('reportes.tesoreria.pdf');
Route::get('/reportes/miembros', [\App\Http\Controllers\ReporteController::class, 'reportarMiembros'])->name('reportes.miembros');
Route::get('/reportes/bautizados', [\App\Http\Controllers\ReporteController::class, 'reportarBautizados'])->name('reportes.bautizados');
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


// Asistencia QR
Route::get('/asistencia/scanner', [\App\Http\Controllers\AsistenciaController::class, 'scanner'])->name('asistencia.scanner');
Route::get('/asistencia/manual', [\App\Http\Controllers\AsistenciaController::class, 'create'])->name('asistencia.manual');
Route::post('/asistencia/registrar', [\App\Http\Controllers\AsistenciaController::class, 'registrar'])->name('asistencia.registrar');

// Simulador de Rol (RBAC)
Route::get('/switch-role/{rol}', function ($rol) {
    if (in_array($rol, ['administrador', 'tesorero', 'lider', 'ujier'])) {
        session(['current_rol' => $rol]);
    }
    return redirect()->route('dashboard')->with('success', 'Nivel de acceso simulado cambiado a: ' . strtoupper($rol));
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
Route::delete('/configuracion/accounts/{id}', [\App\Http\Controllers\ConfiguracionController::class, 'destroyAccount'])->name('configuracion.accounts.destroy')->middleware('role:administrador');
Route::post('/configuracion/accounts/{id}/restore', [\App\Http\Controllers\ConfiguracionController::class, 'restoreAccount'])->name('configuracion.accounts.restore')->middleware('role:administrador');

// Sistema y Mantenimiento (dentro de Configuración - Solo Administradores)
Route::post('/configuracion/sistema', [\App\Http\Controllers\ConfiguracionController::class, 'updateSistema'])->name('sistema.update')->middleware('role:administrador');
Route::post('/configuracion/backup', [\App\Http\Controllers\ConfiguracionController::class, 'backupDatabase'])->name('sistema.backup')->middleware('role:administrador');
Route::post('/configuracion/permisos', [\App\Http\Controllers\ConfiguracionController::class, 'updatePermisos'])->name('permisos.update')->middleware('role:administrador');

