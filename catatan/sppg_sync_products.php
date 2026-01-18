<?php

require_once __DIR__ . '/../config.php';

$sqlProducts = "SELECT id, code, name, unit, is_active FROM products";
$resProducts = $conn->query($sqlProducts);
if (!$resProducts) {
    echo 'Gagal membaca products' . PHP_EOL;
    exit(1);
}

$sqlMat = "INSERT INTO sppg_materials (material_code, material_name, category, subcategory, unit, package_size, shelf_life_months, is_active)
VALUES (?, ?, ?, ?, ?, ?, ?, 1)
ON DUPLICATE KEY UPDATE material_name = VALUES(material_name), unit = VALUES(unit), package_size = VALUES(package_size), is_active = VALUES(is_active)";
$stmtMat = $conn->prepare($sqlMat);
if (!$stmtMat) {
    echo 'Gagal menyiapkan stmt sppg_materials' . PHP_EOL;
    exit(1);
}

$insertMat = 0;
while ($p = $resProducts->fetch_assoc()) {
    $code = $p['code'];
    $name = $p['name'];
    $unit = $p['unit'] !== '' ? $p['unit'] : 'UNIT';
    $category = 'B';
    $subcategory = '';
    $package = '1 ' . $unit;
    $shelf = 6;
    $stmtMat->bind_param('ssssssi', $code, $name, $category, $subcategory, $unit, $package, $shelf);
    if ($stmtMat->execute()) {
        $insertMat++;
    }
}
$stmtMat->close();

$manualMaterials = [
    ['IKAN_NILA', 'Ikan Nila', 'D', 'Ikan Segar', 'KG', '1 KG ± 3 ekor', 1],
    ['GARAM_KASAR', 'Garam Kasar', 'B', 'Garam', 'PAK', '1 PAK', 24],
    ['JERUK_NIPIS', 'Jeruk Nipis', 'B', 'Buah', 'KG', '1 KG', 1],
    ['BAWANG_PUTIH', 'Bawang Putih', 'M', 'Bumbu', 'KG', '1 KG', 6],
    ['CABAI_RAWIT_HIJAU', 'Cabai Rawit Hijau', 'M', 'Bumbu', 'KG', '1 KG', 3],
    ['CABAI_RAWIT_MERAH', 'Cabai Rawit Merah', 'M', 'Bumbu', 'KG', '1 KG', 3],
    ['CABAI_MERAH', 'Cabai Merah', 'M', 'Bumbu', 'KG', '1 KG', 3],
    ['JAHE', 'Jahe', 'M', 'Bumbu', 'KG', '1 KG', 6],
    ['KEMIRI', 'Kemiri', 'M', 'Bumbu', 'KG', '1 KG', 12],
    ['TOMAT', 'Tomat', 'B', 'Sayur', 'KG', '1 KG', 1],
    ['BATANG_RIAS', 'Batang Rias', 'M', 'Bumbu', 'KG', '1 KG', 1],
    ['ANDALIMAN', 'Andaliman', 'M', 'Bumbu', 'KG', '1 KG', 6],
    ['GARAM_HALUS', 'Garam Halus', 'B', 'Garam', 'BUNGKUS', '1 BUNGKUS', 24],
    ['TEMPE', 'Tempe', 'O', 'Protein Nabati', 'PAPAN', '1 PAPAN', 5],
    ['TEPUNG_TERIGU', 'Tepung Terigu', 'B', 'Tepung', 'KG', '1 KG', 12],
    ['KUNYIT_BUBUK', 'Kunyit Bubuk', 'M', 'Bumbu', 'RENCENG', '1 RENCENG (12 sachet)', 12],
    ['PAKCOY', 'Pakcoy', 'B', 'Sayur', 'KG', '1 KG', 1],
    ['SAUS_TIRAM', 'Saus Tiram', 'M', 'Saus', 'BOTOL', '1 BOTOL', 12],
    ['TEPUNG_MAIZENA', 'Tepung Maizena', 'B', 'Tepung', 'KG', '1 KG', 12],
    ['MELON', 'Melon', 'B', 'Buah', 'KG', '1 KG', 0],
];

$stmtMatManual = $conn->prepare($sqlMat);
if ($stmtMatManual) {
    foreach ($manualMaterials as $m) {
        $code = $m[0];
        $name = $m[1];
        $category = $m[2];
        $subcategory = $m[3];
        $unit = $m[4];
        $package = $m[5];
        $shelf = $m[6];
        $stmtMatManual->bind_param('ssssssi', $code, $name, $category, $subcategory, $unit, $package, $shelf);
        $stmtMatManual->execute();
    }
    $stmtMatManual->close();
}

$sqlPlu = "SELECT p.code AS material_code, pc.plu_number, m.is_primary_plu
FROM products p
JOIN product_plu_mapping m ON m.product_id = p.id
JOIN plu_codes pc ON pc.id = m.plu_code_id";
$resPlu = $conn->query($sqlPlu);
if (!$resPlu) {
    echo 'Gagal membaca mapping PLU' . PHP_EOL;
    exit(1);
}

$stmtDel = $conn->prepare("DELETE FROM sppg_material_plu_mappings WHERE material_code = ? AND plu_number = ?");
$stmtIns = $conn->prepare("INSERT INTO sppg_material_plu_mappings (material_code, plu_number, mapping_type, conversion_factor, is_primary) VALUES (?, ?, 'direct', 1.0000, ?)");
if (!$stmtDel || !$stmtIns) {
    echo 'Gagal menyiapkan stmt sppg_material_plu_mappings' . PHP_EOL;
    exit(1);
}

$insertMap = 0;
while ($r = $resPlu->fetch_assoc()) {
    $materialCode = $r['material_code'];
    $plu = $r['plu_number'];
    $isPrimary = (int)$r['is_primary_plu'] === 1 ? 1 : 0;
    $stmtDel->bind_param('ss', $materialCode, $plu);
    $stmtDel->execute();
    $stmtIns->bind_param('ssi', $materialCode, $plu, $isPrimary);
    if ($stmtIns->execute()) {
        $insertMap++;
    }
}
$stmtDel->close();
$stmtIns->close();

echo 'Sinkronisasi sppg_materials: ' . $insertMat . PHP_EOL;
echo 'Sinkronisasi sppg_material_plu_mappings: ' . $insertMap . PHP_EOL;
