<?php
session_start(); // Asegúrate de que la sesión está iniciada
require_once 'config.php'; // Conexión a la base de datos

// Comprobar si la conexión a la base de datos se establece correctamente
if (!$pdo) {
    die("Error de conexión con la base de datos.");
}

// Obtener los productos
$stmt = $pdo->query("SELECT * FROM products");

// Verificar si la sesión está iniciada
$isLoggedIn = isset($_SESSION['username']);

// Obtener el rol del usuario, si está autenticado
$userRole = null;
if ($isLoggedIn) {
    $stmtRole = $pdo->prepare("SELECT role FROM users WHERE username = :username");
    $stmtRole->execute(['username' => $_SESSION['username']]);
    $user = $stmtRole->fetch();
    $userRole = $user['role'] ?? null;
}

// Obtener los productos en el carrito
$cartItems = [];
$totalPrice = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId) {
        $cartStmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $cartStmt->execute(['id' => $productId]);
        $product = $cartStmt->fetch();
        if ($product) {
            $cartItems[] = $product;
            $totalPrice += $product['price'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Zapatillas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Mostrar botones de inicio de sesión o registrar según el estado de la sesión -->
<?php if ($isLoggedIn): ?>
    <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    <?php if ($userRole === 'admin'): ?>
        <a href="admin.php" class="admin-link">Ir al panel de administración</a>
    <?php endif; ?>
    <button class="auth-button" onclick="window.location.href='logout.php'">Cerrar sesión</button>
<?php else: ?>
    <div class="auth-buttons">
        <button class="auth-button" onclick="window.location.href='login.php'">Iniciar sesión</button>
        <button class="auth-button" onclick="window.location.href='register.php'">Registrar</button>
    </div>
<?php endif; ?>

<h1>Kicks</h1>

<!-- Carrito -->
<div class="cart-container" id="cartContainer">
    <button class="cart-toggle-btn" onclick="toggleCart()">Carrito (<?php echo count($cartItems); ?>)</button>
    <div class="cart-items" id="cartItems" style="display: none;">
        <?php if (!empty($cartItems)): ?>
            <ul>
                <?php foreach ($cartItems as $item): ?>
                    <li>
                        <?php echo htmlspecialchars($item['name']); ?> - <?php echo number_format($item['price'], 2); ?>€
                        <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>">Eliminar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total: <?php echo number_format($totalPrice, 2); ?>€</strong></p>
            <button class="cart-toggle-btn">Comprar ya </button>
        <?php else: ?>
            <p>El carrito está vacío.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Productos -->
<div class="product-grid">
    <?php
    if ($stmt) {
        while ($product = $stmt->fetch()) {
            echo "<div class='product'>";
            echo "<img src='" . htmlspecialchars($product['image_url']) . "' alt='" . htmlspecialchars($product['name']) . "' class='product-image'>";
            echo "<h3>" . htmlspecialchars($product['name']) . "</h3>";
            echo "<p>" . htmlspecialchars($product['price']) . "€</p>";
            echo "<a href='cart.php?action=add&id=" . htmlspecialchars($product['id']) . "'>Añadir al carrito</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No se pudieron cargar los productos.</p>";
    }
    ?>
</div>

<script src="script.js"></script>
</body>
</html>
