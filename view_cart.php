<?php
session_start();
require_once 'config.php';

// Verificar acción de eliminar
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $productIdToRemove = $_GET['id'];

    // Eliminar el producto del carrito
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($productIdToRemove) {
            return $item != $productIdToRemove;
        });
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reorganizar el array al borrrar algo del carritop
    }

    // Redirigir a la página del carrito después de eliminar
    header('Location: view_cart.php');
    exit;
}

// Verificar si el carrito está vacío
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo "Tu carrito está vacío.";
    exit;
}

// Obtener los IDs de los productos en el carrito
$cartItems = $_SESSION['cart'];
$cartProductIds = implode(",", $cartItems);

// Obtener productos desde la base de datos
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($cartProductIds)");

$total = 0;
echo "<h2>Tu carrito:</h2>";
echo "<ul>";

// Mostrar los productos en el carrito con un botón para eliminar
while ($product = $stmt->fetch()) {
    echo "<li>";
    echo $product['name'] . " - Precio: $" . $product['price'];
    echo " <a href='view_cart.php?action=remove&id=" . $product['id'] . "'>Eliminar</a>";  // Botón de eliminar
    echo "</li>";
    $total += $product['price'];
}

echo "</ul>";

echo "<p>Total: $" . $total . "</p>";

?>

<a href="index.php">Volver a la tienda</a>
