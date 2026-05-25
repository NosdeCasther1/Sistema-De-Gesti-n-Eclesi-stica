<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

$user = Usuario::where('email', 'admin@iglesia.com')->first();
if ($user) {
    Auth::login($user);
    $request->session()->regenerate();
    echo "Logged in successfully as " . $user->email . ". Redirecting...";
    echo "<script>window.location.href = '/configuracion';</script>";
} else {
    echo "User not found.";
}
