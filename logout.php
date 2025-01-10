<?php
session_start();
session_destroy();  // salir de la sesión
header('Location: index.php');  // Redirigir al índice
exit;
?>
