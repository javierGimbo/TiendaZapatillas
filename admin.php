<?php
session_start();
require_once 'config.php';

// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Obtener el rol del usuario
$stmt = $pdo->prepare("SELECT role FROM users WHERE username = :username");
$stmt->execute(['username' => $_SESSION['username']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    echo "Acceso denegado. Solo los administradores pueden acceder a esta página.";
    exit;
}

// Eliminar usuario o producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['delete_user'];
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $userId]);
        echo "Usuario eliminado.";
    }

    if (isset($_POST['delete_product'])) {
        $productId = $_POST['delete_product'];
        $pdo->prepare("DELETE FROM products WHERE id = :id")->execute(['id' => $productId]);
        echo "Producto eliminado.";
    }
}

// Obtener listas de usuarios y productos
$users = $pdo->query("SELECT id, username, role FROM users")->fetchAll();
$products = $pdo->query("SELECT id, name, price FROM products")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
</head>
<body>

<h1>Panel de Administración</h1>
<p>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> (Administrador)</p>
<a href="index.php">Volver a la tienda</a>
<a href="logout.php">Cerrar sesión</a>

<h2>Gestión de Usuarios</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Acción</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <?php if ($user['role'] !== 'admin'): ?>
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="delete_user" value="<?php echo $user['id']; ?>">Eliminar</button>
                    </form>
                <?php else: ?>
                    <em>No se puede eliminar</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Gestión de Productos</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Acción</th>
    </tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo $product['id']; ?></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?>€</td>
            <td>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="delete_product" value="<?php echo $product['id']; ?>">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
