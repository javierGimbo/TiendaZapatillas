<?php
session_start();  // Asegúrate de que la sesión está iniciada

// Verificar si el parámetro 'action' es 'add' y si existe el parámetro 'id'
if (isset($_GET['action']) && $_GET['action'] == 'add' && isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Si no existe un carrito en la sesión, lo creamos
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Agregar el ID del producto al carrito
    $_SESSION['cart'][] = $productId;

    // Redirigir al usuario de nuevo a la página principal (index.php)
    header('Location: index.php');
    exit;
}

// Verificar si el parámetro 'action' es 'remove' y si existe el parámetro 'id'
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Eliminar el producto del carrito
    if (($key = array_search($productId, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }

    // Redirigir al usuario de vuelta al carrito
    header('Location: index.php');
    exit;
}
?>
