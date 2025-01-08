<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Buscar usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Usuario no encontrado.";
    } else {
        // Verificar la contraseña con password_verify
        if (password_verify($password, $user['password'])) {
            // La contraseña es correcta, iniciar sesión
            $_SESSION['username'] = $user['username'];
            header('Location: index.php'); // Redirigir a la página principal
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>

<h1>Iniciar Sesión</h1>

<form method="POST">
    <label for="username">Usuario:</label>
    <input type="text" name="username" required><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required><br>

    <button type="submit">Iniciar Sesión</button>
</form>

</body>
</html>


