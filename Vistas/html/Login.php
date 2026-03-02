<?php
session_start();

// Si ya hay sesión iniciada, redirigir a Principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: /ProyectoIglesia/Vistas/html/index.php");
    exit();
}

require_once __DIR__ . '/../../Config/conexion.php';
$conn = getDBConnection();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Buscar el usuario por email
    $query = "SELECT id_usuario, nombres, username, password, role, status FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verificar si está activo
        if ($row['status'] === 'activo') {
            // Verificar contraseña
            if (password_verify($password, $row['password'])) {
                // Iniciar sesión
                $_SESSION['usuario_id'] = $row['id_usuario'];
                $_SESSION['nombres'] = $row['nombres'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['rol'] = $row['role'];

                // Redirigir a Principal
                header("Location: /ProyectoIglesia/Vistas/html/index.php");
                exit();
            } else {
                $error_message = "Credenciales incorrectas.";
            }
        } else {
            $error_message = "Su cuenta está desactivada. Contacte al administrador.";
        }
    } else {
        $error_message = "Credenciales incorrectas.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            /* Fondo Premium: Un gris/beige muy suave y cálido */
            background-color: #f7f6f5;
        }

        .container {
            display: flex;
            width: 850px;
            height: 480px;
            background-color: #ffffff;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            /* Sombra súper suave */
            border-radius: 20px;
            overflow: hidden;
        }

        .welcome-section {
            flex: 1.1;
            /* Sección Izquierda: Un ligero tono crema/champaña en vez de dorado fuerte */
            background: linear-gradient(135deg, #fdfaf4, #f4ecd8);
            color: #4a2c2c;
            /* Texto oscuro, ligeramente marrón */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            text-align: center;
            border-right: 1px solid rgba(0, 0, 0, 0.03);
        }

        .welcome-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/ProyectoIglesia/img/logo.png') no-repeat center center;
            background-size: 80%;
            opacity: 0.04;
            /* Logo de fondo apenas perceptible */
            pointer-events: none;
        }

        .logo {
            margin-bottom: 20px;
            z-index: 2;
        }

        .welcome-section h1 {
            font-size: 2.2rem;
            margin-bottom: 12px;
            font-weight: 700;
            color: #9F191E;
            /* Rojo corporativo sutil para el título */
            z-index: 2;
        }

        .welcome-section p {
            font-size: 1.05rem;
            line-height: 1.6;
            color: #6c5a5a;
            z-index: 2;
        }

        .login-section {
            flex: 1;
            background: #ffffff;
            /* Blanco puro para contraste limpio */
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-section form {
            width: 100%;
            max-width: 320px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background-color: #fafbfa;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            background-color: #ffffff;
            border-color: #CF9B48;
            /* Borde dorado corporativo suave */
            outline: none;
            box-shadow: 0 0 0 4px rgba(207, 155, 72, 0.15);
        }

        button {
            width: 100%;
            padding: 14px;
            /* Botón Login: Rojo corporativo mate */
            background-color: #9F191E;
            border: none;
            color: #ffffff;
            font-weight: bold;
            font-size: 1.05rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(159, 25, 30, 0.2);
        }

        button:hover {
            background-color: #8A151A;
            /* Un poco más oscuro */
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(159, 25, 30, 0.3);
        }

        .actions {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-top: 25px;
        }

        .actions a {
            color: #7b8a8b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .actions a:hover {
            color: #9F191E;
            /* Rojo corporativo al pasar el mouse */
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="welcome-section">
            <div class="logo">

                <img src="/ProyectoIglesia/img/logo.png" alt="Logo" style="max-width: 150px;">
            </div>
            <h1>Hola, Bienvenido!</h1>
            <p>Este es un sistema web para el control de miembros, ingresos y egresos de la iglesia.</p>
        </div>
        <div class="login-section">
            <?php if (!empty($error_message)): ?>
                <div
                    style="background-color: #fdf2f2; color: #9F191E; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; width: 100%; max-width: 320px; font-size: 0.95rem; border-left: 4px solid #9F191E;">
                    <strong>Error:</strong>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
                <div class="actions">
                    <a href="#">Recuperar Contraseña?</a>
                    <a href="/ProyectoIglesia/Vistas/html/Usuarios.php">Registrarse</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>