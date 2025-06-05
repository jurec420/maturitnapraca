<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}
$orderId = (int)$_GET['id'];
$db = getDb();
$stmt = $db->prepare('SELECT orders.id, orders.user_id, orders.created_at, users.username FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['id'] != $order['user_id'])) {
    header('Location: orders.php');
    exit;
}
$itemStmt = $db->prepare('SELECT products.name, order_items.quantity, order_items.price FROM order_items JOIN products ON products.id = order_items.product_id WHERE order_items.order_id = ?');
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
include 'header.php';
?>
<h1>Order #<?php echo $order['id']; ?></h1>
<p>Customer: <?php echo htmlspecialchars($order['username']); ?></p>
<p>Date: <?php echo $order['created_at']; ?></p>
<table>
<tr><th>Product</th><th>Qty</th><th>Price</th></tr>
<?php foreach($items as $i): ?>
<tr>
    <td><?php echo htmlspecialchars($i['name']); ?></td>
    <td><?php echo $i['quantity']; ?></td>
    <td><?php echo number_format($i['price'],2); ?> €</td>
</tr>
<?php endforeach; ?>
</table>
<?php include 'footer.php'; ?>
