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

// CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Eliminar usuario
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['delete_user'];
        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $userId]);
        echo "Usuario eliminado.";
    }

    // Eliminar producto
    if (isset($_POST['delete_product'])) {
        $productId = $_POST['delete_product'];
        $pdo->prepare("DELETE FROM products WHERE id = :id")->execute(['id' => $productId]);
        echo "Producto eliminado.";
    }

    // Crear nuevo usuario
    if (isset($_POST['create_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);
        echo "Usuario creado exitosamente.";
    }

    // Crear nuevo producto
    if (isset($_POST['create_product'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image_url = $_POST['image_url'];

        $stmt = $pdo->prepare("INSERT INTO products (name, price, image_url) VALUES (:name, :price, :image_url)");
        $stmt->execute(['name' => $name, 'price' => $price, 'image_url' => $image_url]);
        echo "Producto creado exitosamente.";
    }

    // Modificar usuario
    if (isset($_POST['edit_user'])) {
        $userId = $_POST['user_id'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
        $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role, 'id' => $userId]);
        echo "Usuario actualizado exitosamente.";
    }

    // Modificar producto
    if (isset($_POST['edit_product'])) {
        $productId = $_POST['product_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image_url = $_POST['image_url'];

        $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, image_url = :image_url WHERE id = :id");
        $stmt->execute(['name' => $name, 'price' => $price, 'image_url' => $image_url, 'id' => $productId]);
        echo "Producto actualizado exitosamente.";
    }
}

// Obtener listas de usuarios y productos
$users = $pdo->query("SELECT id, username, role FROM users")->fetchAll();
$products = $pdo->query("SELECT id, name, price FROM products")->fetchAll();

// Verificar si hay un usuario o producto que se va a editar
$editUser = null;
$editProduct = null;

if (isset($_GET['edit_user_id'])) {
    $editUserId = $_GET['edit_user_id'];
    $editUser = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $editUser->execute(['id' => $editUserId]);
    $editUser = $editUser->fetch();
}

if (isset($_GET['edit_product_id'])) {
    $editProductId = $_GET['edit_product_id'];
    $editProduct = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $editProduct->execute(['id' => $editProductId]);
    $editProduct = $editProduct->fetch();
}

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
            <td><?php echo htmlspecialchars($user['username']); ?></td>   <!--htmlspecialchars sirve para convertir caracteres especiales en HTML -->
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <?php if ($user['role'] !== 'admin'): ?>
                    <form method="POST" style="display:inline;">
                        <button type="submit" name="delete_user" value="<?php echo $user['id']; ?>">Eliminar</button>
                    </form>
                    <a href="?edit_user_id=<?php echo $user['id']; ?>">Editar</a>
                <?php else: ?>
                    <em>No se puede eliminar</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Crear Usuario</h3>
<form method="POST">
    <label for="username">Usuario:</label>
    <input type="text" name="username" required><br>

    <label for="password">Contraseña:</label>
    <input type="password" name="password" required><br>

    <label for="role">Rol:</label>
    <select name="role" required>
        <option value="user">Usuario</option>
        <option value="admin">Administrador</option>
    </select><br>

    <button type="submit" name="create_user">Crear Usuario</button>
</form>

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
                <a href="?edit_product_id=<?php echo $product['id']; ?>">Editar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Crear Producto</h3>
<form method="POST">
    <label for="name">Nombre:</label>
    <input type="text" name="name" required><br>

    <label for="price">Precio:</label>
    <input type="number" step="0.01" name="price" required><br>

    <label for="image_url">URL de la Imagen:</label>
    <input type="text" name="image_url" required><br>

    <button type="submit" name="create_product">Crear Producto</button>
</form>

<!-- Formulario de edición de usuario -->
<?php if ($editUser): ?>
    <h3>Editar Usuario</h3>
    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $editUser['id']; ?>">
        <label for="username">Usuario:</label>
        <input type="text" name="username" value="<?php echo $editUser['username']; ?>" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required><br>

        <label for="role">Rol:</label>
        <select name="role" required>
            <option value="user" <?php echo $editUser['role'] === 'user' ? 'selected' : ''; ?>>Usuario</option>
            <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select><br>

        <button type="submit" name="edit_user">Actualizar Usuario</button>
    </form>
<?php endif; ?>

<!-- Formulario de edición de producto -->
<?php if ($editProduct): ?>
    <h3>Editar Producto</h3>
    <form method="POST">
        <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>">
        <label for="name">Nombre:</label>
        <input type="text" name="name" value="<?php echo $editProduct['name']; ?>" required><br>

        <label for="price">Precio:</label>
        <input type="number" step="0.01" name="price" value="<?php echo $editProduct['price']; ?>" required><br>

        <label for="image_url">URL de la Imagen:</label>
        <input type="text" name="image_url" value="<?php echo $editProduct['image_url']; ?>" required><br>

        <button type="submit" name="edit_product">Actualizar Producto</button>
    </form>
<?php endif; ?>

</body>
</html>
