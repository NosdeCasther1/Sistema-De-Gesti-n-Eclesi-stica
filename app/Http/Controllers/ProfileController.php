<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the user's profile edit form.
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Invalidate other sessions (logout from other devices).
     */
    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.current_password' => 'La contraseña ingresada es incorrecta.',
        ]);

        // Esto destruye cualquier otra sesión activa en otros navegadores/dispositivos
        Auth::logoutOtherDevices($request->password);

        return back()->with('status', 'Todas las sesiones en otros dispositivos han sido cerradas por seguridad.');
    }
}
