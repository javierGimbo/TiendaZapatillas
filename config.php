<?php
$host = 'localhost';  // Cambia esto si tienes otro host
$db = 'tienda_zapatillas'; // Nombre de la base de datos
$user = 'root'; // Tu usuario de MySQL
$pass = ''; // Tu contraseña de MySQL (si usas XAMPP o WAMP, generalmente está vacío)

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>


<!-- /*http://localhost/actividades/prueba/index.php --> 
