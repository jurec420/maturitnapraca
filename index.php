<?php
session_start();
require_once 'db.php';
$db = getDb();
$products = $db->query('SELECT * FROM products')->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header('Location: index.php');
    exit;
}
include 'header.php';
?>
<h1>Products</h1>
<div>
<?php foreach ($products as $p): ?>
    <div style="margin-bottom:15px;">
        <strong><?php echo htmlspecialchars($p['name']); ?></strong> -
        <?php echo number_format($p['price'],2); ?> €
        <a href="?add=<?php echo $p['id']; ?>">Add to cart</a>
        <p><?php echo htmlspecialchars($p['description']); ?></p>
    </div>
<?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
