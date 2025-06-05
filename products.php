<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
$db = getDb();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $desc = trim($_POST['description'] ?? '');
    if ($name && $price) {
        $stmt = $db->prepare('INSERT INTO products (name, price, description) VALUES (?, ?, ?)');
        $stmt->execute([$name, $price, $desc]);
        header('Location: products.php');
        exit;
    } else {
        $error = 'Name and price required';
    }
}
$products = $db->query('SELECT * FROM products')->fetchAll(PDO::FETCH_ASSOC);
include 'header.php';
?>
<h1>Manage Products</h1>
<?php if ($error): ?><p style="color:red;"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
<form method="post">
    <input type="text" name="name" placeholder="Name" required>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="text" name="description" placeholder="Description">
    <button type="submit">Add Product</button>
</form>
<table>
<tr><th>Name</th><th>Price</th><th>Description</th></tr>
<?php foreach($products as $p): ?>
<tr>
    <td><?php echo htmlspecialchars($p['name']); ?></td>
    <td><?php echo number_format($p['price'],2); ?> €</td>
    <td><?php echo htmlspecialchars($p['description']); ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>
