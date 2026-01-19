<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'excel_reader.php';

$file = __DIR__ . DIRECTORY_SEPARATOR . 'excel' . DIRECTORY_SEPARATOR . 'SIKLUS MENU 12 HARI, NILAI GIZI, BAHAN.xlsx';

if (!is_file($file)) {
    echo 'File tidak ditemukan: ' . $file . PHP_EOL;
    exit(1);
}

$sheets = excel_list_sheets($file);
if (empty($sheets)) {
    $sheetPath = 'xl/worksheets/sheet1.xml';
} else {
    $sheetPath = $sheets[0]['path'];
}
$rows = read_excel_sheet($file, $sheetPath, 1000);

$items = [];
$currentDay = null;
$currentPm = null;
$currentGroup = null;

foreach ($rows as $row) {
    $trimmed = [];
    foreach ($row as $cell) {
        $trimmed[] = trim((string)$cell);
    }
    foreach ($trimmed as $cell) {
        if ($cell === '') {
            continue;
        }
        $upper = strtoupper($cell);
        if (strpos($upper, 'HARI ') === 0) {
            $numPart = trim(substr($upper, 5));
            $num = (int)$numPart;
            if ($num > 0) {
                $currentDay = $num;
            }
        }
        if (preg_match('/PM\s*=\s*(\d+)/i', $cell, $m)) {
            $currentPm = (int)$m[1];
        }
        if (strpos($upper, 'BAHAN ') === 0 || $upper === 'BAHAN' || $upper === 'BUAH') {
            $currentGroup = $cell;
        }
    }
    $values = [];
    foreach ($trimmed as $cell) {
        if ($cell !== '') {
            $values[] = $cell;
        }
    }
    if (count($values) < 3) {
        continue;
    }
    $idxRaw = $values[0];
    $nameRaw = $values[1];
    $qtyRaw = $values[2];
    if (!ctype_digit(str_replace(['+', '-'], '', $idxRaw))) {
        continue;
    }
    if ($nameRaw === '') {
        continue;
    }
    if (!is_numeric(str_replace(',', '.', $qtyRaw))) {
        continue;
    }
    $unit = '';
    if (isset($values[3])) {
        $unit = $values[3];
    }
    $qty = (float)str_replace(',', '.', $qtyRaw);
    $items[] = [
        'day' => $currentDay,
        'pm_target' => $currentPm,
        'group' => $currentGroup,
        'index' => (int)$idxRaw,
        'item_name' => $nameRaw,
        'quantity' => $qty,
        'unit' => $unit,
    ];
}

$manualMap = [
    'beras' => 'BERAS_MEDIUM',
    'minyak' => 'MINYAK_GORENG_SAWIT',
    'gula pasir' => 'GULA_PASIR',
    'telur' => 'TELUR_AYAM_RAS',
    'bawang merah' => 'BAWANG_MERAH',
    'ikan nila' => 'IKAN_NILA',
    'garam kasar' => 'GARAM_KASAR',
    'jeruk nipis' => 'JERUK_NIPIS',
    'bawang putih' => 'BAWANG_PUTIH',
    'cabe rawit hijau' => 'CABAI_RAWIT_HIJAU',
    'cabai rawit hijau' => 'CABAI_RAWIT_HIJAU',
    'cabe rawit merah' => 'CABAI_RAWIT_MERAH',
    'cabai rawit merah' => 'CABAI_RAWIT_MERAH',
    'cabe merah' => 'CABAI_MERAH',
    'cabai merah' => 'CABAI_MERAH',
    'jahe' => 'JAHE',
    'kemiri' => 'KEMIRI',
    'tomat' => 'TOMAT',
    'batang rias' => 'BATANG_RIAS',
    'rias' => 'BATANG_RIAS',
    'andaliman' => 'ANDALIMAN',
    'garam halus' => 'GARAM_HALUS',
    'tempe' => 'TEMPE',
    'tepung terigu' => 'TEPUNG_TERIGU',
    'kunyit bubuk' => 'KUNYIT_BUBUK',
    'pakcoy' => 'PAKCOY',
    'saus tiram' => 'SAUS_TIRAM',
    'tepung maizena' => 'TEPUNG_MAIZENA',
    'melon' => 'MELON',
];

$codes = [];
foreach ($items as $it) {
    $nameKey = strtolower($it['item_name']);
    $code = null;
    foreach ($manualMap as $needle => $pcode) {
        if (strpos($nameKey, $needle) !== false) {
            $code = $pcode;
            break;
        }
    }
    if ($code !== null) {
        $codes[$code] = true;
    }
}

$productInfo = [];
if (!empty($codes)) {
    $placeholders = implode(',', array_fill(0, count($codes), '?'));
    $sql = "SELECT p.id, p.code, p.name, (SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = p.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number FROM products p WHERE p.code IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $types = str_repeat('s', count($codes));
        $codesArr = array_keys($codes);
        $stmt->bind_param($types, ...$codesArr);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $productInfo[$row['code']] = [
                    'id' => (int)$row['id'],
                    'code' => $row['code'],
                    'name' => $row['name'],
                    'plu_number' => $row['plu_number'],
                ];
            }
        }
        $stmt->close();
    }
}

$output = [];
foreach ($items as $it) {
    $nameKey = strtolower($it['item_name']);
    $code = null;
    foreach ($manualMap as $needle => $pcode) {
        if (strpos($nameKey, $needle) !== false) {
            $code = $pcode;
            break;
        }
    }
    $prod = null;
    if ($code !== null && isset($productInfo[$code])) {
        $prod = $productInfo[$code];
    }
    $it['product_code'] = $code;
    $it['product'] = $prod;
    $output[] = $it;
}

$outFile = __DIR__ . '/output_bahan_siklus12.json';
file_put_contents($outFile, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$summary = [];
foreach ($output as $it) {
    if (!isset($it['product_code']) || $it['product_code'] === null) {
        continue;
    }
    $code = $it['product_code'];
    if (!isset($summary[$code])) {
        $name = $it['item_name'];
        $plu = null;
        if ($it['product'] !== null) {
            $name = $it['product']['name'];
            $plu = $it['product']['plu_number'];
        }
        $summary[$code] = [
            'product_code' => $code,
            'product_name' => $name,
            'plu_number' => $plu,
            'day' => $it['day'],
            'pm_target' => $it['pm_target'],
            'total_quantity' => 0.0,
            'unit' => $it['unit'],
        ];
    }
    $summary[$code]['total_quantity'] += $it['quantity'];
}

$day1ManualQuantities = [
    'BERAS_MEDIUM' => 130.0,
    'IKAN_NILA' => 260.0,
    'GARAM_KASAR' => 3.0,
    'JERUK_NIPIS' => 10.0,
    'MINYAK_GORENG_SAWIT' => 65.0,
    'BAWANG_MERAH' => 6.0,
    'BAWANG_PUTIH' => 4.0,
    'CABAI_MERAH' => 8.0,
];
foreach ($day1ManualQuantities as $code => $qty) {
    if (isset($summary[$code])) {
        if ((int)$summary[$code]['day'] === 1) {
            $summary[$code]['total_quantity'] = $qty;
        }
    }
}

$summaryFile = __DIR__ . '/output_bahan_siklus12_summary.json';
file_put_contents($summaryFile, json_encode(array_values($summary), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$sppgId = 'SPPG_JAN2026';
$baseDate = '2026-01-01';
$targetGroup = 'anak';

$unitMap = [
    'kg' => 1000.0,
    'pak' => 1000.0,
    'bungkus' => 500.0,
    'papan' => 5000.0,
    'botol' => 133.0,
    'renceng' => 120.0,
];

foreach ($summary as $row) {
    $unitKey = strtolower($row['unit']);
    if (!isset($unitMap[$unitKey])) {
        continue;
    }
    $day = (int)$row['day'];
    $pmTarget = (int)$row['pm_target'];
    $materialCode = $row['product_code'];
    if ($materialCode === null || $day <= 0 || $pmTarget <= 0) {
        continue;
    }
    $dt = new DateTime($baseDate);
    if ($day > 1) {
        $dt->modify('+' . ($day - 1) . ' day');
    }
    $demandDate = $dt->format('Y-m-d');
    $grams = (float)$row['total_quantity'] * $unitMap[$unitKey];
    $stmtDel = $conn->prepare("DELETE FROM sppg_daily_material_demand WHERE sppg_id = ? AND demand_date = ? AND material_code = ? AND target_group = ?");
    if ($stmtDel) {
        $stmtDel->bind_param('ssss', $sppgId, $demandDate, $materialCode, $targetGroup);
        $stmtDel->execute();
        $stmtDel->close();
    }
    $stmtIns = $conn->prepare("INSERT INTO sppg_daily_material_demand (sppg_id, demand_date, material_code, target_group, beneficiaries_count, total_quantity_grams) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmtIns) {
        $stmtIns->bind_param('ssssid', $sppgId, $demandDate, $materialCode, $targetGroup, $pmTarget, $grams);
        $stmtIns->execute();
        $stmtIns->close();
    }
}

$mappedCount = 0;
foreach ($output as $it) {
    if ($it['product'] !== null) {
        $mappedCount++;
    }
}

echo 'Total baris bahan: ' . count($output) . PHP_EOL;
echo 'Terpetakan ke produk: ' . $mappedCount . PHP_EOL;
echo 'Output JSON: ' . $outFile . PHP_EOL;
echo 'Ringkasan per produk: ' . $summaryFile . PHP_EOL;
