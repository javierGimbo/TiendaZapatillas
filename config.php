<?php
$host = 'localhost';  
$db = 'tienda_zapatillas'; // nombre de la base de datos
$user = 'root'; 
$pass = ''; 

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>



