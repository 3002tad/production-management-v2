<?php
// Simple verification script to print materials and product BOM info
// Usage: php scripts/verify_product_bom.php [product_id]

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'db_production';

$pid = $argv[1] ?? '1001';

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_errno) {
    echo "DB connect failed: " . $mysqli->connect_error . "\n";
    exit(1);
}

echo "Materials (id, name, stock, uom):\n";
$res = $mysqli->query("SELECT id_material, material_name, stock, uom FROM material ORDER BY id_material ASC LIMIT 50");
while ($r = $res->fetch_assoc()) {
    printf("  %s: %s - %s %s\n", $r['id_material'], $r['material_name'], $r['stock'], $r['uom']);
}

echo "\nProduct {$pid} BOM details:\n";
$res = $mysqli->query("SELECT id_product, product_name, bom FROM product WHERE id_product = '" . $mysqli->real_escape_string($pid) . "' LIMIT 1");
if ($res->num_rows === 0) {
    echo "  Product not found\n";
    exit(0);
}
$prod = $res->fetch_assoc();
$bom = json_decode($prod['bom'], true) ?? [];
if (empty($bom)) {
    echo "  No BOM defined for product {$pid}\n";
    exit(0);
}

foreach ($bom as $item) {
    $mid = $item['id_material'] ?? null;
    $name = $item['material_name'] ?? '(no name)';
    $qty = $item['quantity_per_unit'] ?? 'N/A';
    $uom = $item['uom'] ?? '';
    if ($mid) {
        $mr = $mysqli->query("SELECT stock FROM material WHERE id_material = '" . $mysqli->real_escape_string($mid) . "' LIMIT 1");
        $m = $mr->fetch_assoc();
        $stock = $m['stock'] ?? '(not found)';
    } else {
        $stock = '(custom material)';
    }
    printf("  id=%s name=%s qty=%s uom=%s stock=%s\n", $mid ?? 'null', $name, $qty, $uom, $stock);
}

$mysqli->close();
