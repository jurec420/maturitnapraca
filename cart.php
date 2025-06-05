<?php
session_start();
require_once 'db.php';
$db = getDb();
$cart = $_SESSION['cart'] ?? [];
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    if (isset($cart[$id])) {
        unset($cart[$id]);
        $_SESSION['cart'] = $cart;
    }
    header('Location: cart.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cart) && isset($_SESSION['user'])) {
    $db->beginTransaction();
    $stmt = $db->prepare('INSERT INTO orders (user_id) VALUES (?)');
    $stmt->execute([$_SESSION['user']['id']]);
    $orderId = $db->lastInsertId();
    $itemStmt = $db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
    $prodStmt = $db->prepare('SELECT price FROM products WHERE id = ?');
    foreach ($cart as $pid => $qty) {
        $prodStmt->execute([$pid]);
        $price = $prodStmt->fetchColumn();
        $itemStmt->execute([$orderId, $pid, $qty, $price]);
    }
    $db->commit();
    $_SESSION['cart'] = [];
    header('Location: orders.php');
    exit;
}
$ids = array_keys($cart);
$products = [];
if ($ids) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
include 'header.php';
?>
<h1>Your Cart</h1>
<?php if (!$products): ?>
<p>Cart is empty.</p>
<?php else: ?>
<table>
<tr><th>Product</th><th>Qty</th><th>Price</th><th></th></tr>
<?php foreach ($products as $p): ?>
<tr>
    <td><?php echo htmlspecialchars($p['name']); ?></td>
    <td><?php echo $cart[$p['id']]; ?></td>
    <td><?php echo number_format($p['price'],2); ?> €</td>
    <td><a href="?remove=<?php echo $p['id']; ?>">Remove</a></td>
</tr>
<?php endforeach; ?>
</table>
<?php if(isset($_SESSION['user'])): ?>
<form method="post"><button type="submit">Place order</button></form>
<?php else: ?>
<p><a href="login.php">Login</a> to place order.</p>
<?php endif; ?>
<?php endif; ?>
<?php include 'footer.php'; ?>
