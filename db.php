<?php
function getDb() {
    $db = new PDO('sqlite:' . __DIR__ . '/data.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

function initDb() {
    $db = getDb();
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT,
        role TEXT DEFAULT 'user'
    );
    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        price REAL,
        description TEXT
    );
    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER,
        product_id INTEGER,
        quantity INTEGER,
        price REAL
    );");
    $count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        $stmt = $db->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
        $products = [
            ['Notebook', 2.50, 'A5 lined notebook'],
            ['Pen pack', 1.20, 'Set of 3 blue pens'],
            ['Pencil', 0.50, 'HB pencil'],
            ['Eraser', 0.30, 'Standard eraser']
        ];
        foreach ($products as $p) {
            $stmt->execute($p);
        }
    }
}
initDb();
?>
