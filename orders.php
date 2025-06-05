<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$db = getDb();
if ($_SESSION['user']['role'] === 'admin') {
    $stmt = $db->query('SELECT orders.id, users.username, orders.created_at FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC');
} else {
    $stmt = $db->prepare('SELECT id, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$_SESSION['user']['id']]);
}
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
include 'header.php';
?>
<h1>Orders</h1>
<?php if(!$orders): ?>
<p>No orders yet.</p>
<?php else: ?>
<table>
<tr><th>ID</th><?php if($_SESSION['user']['role']==='admin') echo '<th>User</th>'; ?><th>Date</th><th></th></tr>
<?php foreach($orders as $o): ?>
<tr>
    <td><?php echo $o['id']; ?></td>
    <?php if(isset($o['username'])): ?><td><?php echo htmlspecialchars($o['username']); ?></td><?php endif; ?>
    <td><?php echo $o['created_at']; ?></td>
    <td><a href="order.php?id=<?php echo $o['id']; ?>">Detail</a></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php include 'footer.php'; ?>
