<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'excel_reader.php';

function sppg_invoice_manual_map()
{
    return [
        'beras' => 'BERAS_MEDIUM',
        'minyak' => 'MINYAK_GORENG_SAWIT',
        'ayam' => 'DAGING_AYAM',
        'ikan nila' => 'IKAN_NILA',
        'gula pasir' => 'GULA_PASIR',
        'gula ' => 'GULA_PASIR',
        'gula' => 'GULA_PASIR',
        'bawang merah' => 'BAWANG_MERAH',
        'garam kasar' => 'GARAM_KASAR',
        'garam halus' => 'GARAM_HALUS',
        'garam ' => 'GARAM_HALUS',
        'jeruk nipis' => 'JERUK_NIPIS',
        'daun jeruk' => 'DAUN_JERUK',
        'jeruk' => 'JERUK_BUAH',
        'serai' => 'SERAI',
        'bawang putih bubuk' => 'BAWANG_PUTIH_BUBUK',
        'bawang putih' => 'BAWANG_PUTIH',
        'bawang ptih' => 'BAWANG_PUTIH',
        'bawang bombai' => 'BAWANG_BOMBAI',
        'daun salam' => 'DAUN_SALAM',
        'bumbu racik rendang' => 'BUMBU_RACIK_RENDANG',
        'daun kunyit' => 'DAUN_KUNYIT',
        'daun bawang' => 'DAUN_BAWANG',
        'santan kental' => 'SANTAN_KENTAL',
        'kecap manis' => 'KECAP_MANIS',
        'kecap asin' => 'KECAP_ASIN',
        'cabe rawit hijau' => 'CABAI_RAWIT_HIJAU',
        'cabai rawit hijau' => 'CABAI_RAWIT_HIJAU',
        'cabe rawit merah' => 'CABAI_RAWIT_MERAH',
        'cabai rawit merah' => 'CABAI_RAWIT_MERAH',
        'cabe rawit' => 'CABAI_RAWIT_MERAH',
        'cabe hijau' => 'CABAI_HIJAU',
        'cabe merah' => 'CABAI_MERAH',
        'cabai merah' => 'CABAI_MERAH',
        'jahe' => 'JAHE',
        'kemiri' => 'KEMIRI',
        'tomat' => 'TOMAT',
        'batang rias' => 'BATANG_RIAS',
        'rias' => 'BATANG_RIAS',
        'andaliman' => 'ANDALIMAN',
        'tempe' => 'TEMPE',
        'tahu' => 'TAHU',
        'tepung terigu' => 'TEPUNG_TERIGU',
        'kunyit bubuk' => 'KUNYIT_BUBUK',
        'ketumbar bubuk' => 'KETUMBAR_BUBUK',
        'pakcoy' => 'PAKCOY',
        'kangkung' => 'KANGKUNG',
        'saus tiram' => 'SAUS_TIRAM',
        'saos tiram' => 'SAUS_TIRAM',
        'kaldu bubuk' => 'KALDU_BUBUK',
        'terasi' => 'TERASI',
        'tepung maizena' => 'TEPUNG_MAIZENA',
        'melon' => 'MELON',
        'telur' => 'TELUR_AYAM_RAS',
        'labu siam' => 'LABU_SIAM',
        'semangka' => 'SEMANGKA',
        'kunyit ' => 'KUNYIT',
        'sawi putih' => 'SAWI_PUTIH',
        'salak' => 'SALAK',
        'lada' => 'LADA',
        'tomat hijau' => 'TOMAT_HIJAU',
        'ayam filet' => 'DAGING_AYAM_FILET',
        'kentang' => 'KENTANG',
        'jagung' => 'JAGUNG',
        'wortel' => 'WORTEL',
        'buncis' => 'BUNCIS',
        'duku' => 'DUKU',
        'roti' => 'ROTI',
        'susu uht 115 mill' => 'SUSU_UHT_115',
        'susu uht 125 mill' => 'SUSU_UHT_125',
        'pisang' => 'PISANG',
    ];
}

function sppg_invoice_unit_map()
{
    return [
        'kg' => 1000.0,
        'pak' => 1000.0,
        'bungkus' => 500.0,
        'papan' => 5000.0,
        'botol' => 133.0,
        'renceng' => 120.0,
        'renteng' => 120.0,
        'pcs' => 133.0,
        'liter' => 1000.0,
        'sachet' => 50.0,
        'dus' => 1000.0,
        'sisir' => 1000.0,
    ];
}

function sppg_to_title_case($text)
{
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_convert_case') && function_exists('mb_strtolower')) {
        return mb_convert_case(mb_strtolower($text, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
    return ucwords(strtolower($text));
}

function sppg_pick_category_id_for_material($materialName, $categories)
{
    if (empty($categories)) {
        return 0;
    }
    $nameLower = function_exists('mb_strtolower') ? mb_strtolower($materialName, 'UTF-8') : strtolower($materialName);
    foreach ($categories as $cat) {
        $catName = $cat['name'] ?? '';
        if ($catName === '') {
            continue;
        }
        $catLower = function_exists('mb_strtolower') ? mb_strtolower($catName, 'UTF-8') : strtolower($catName);
        if (strpos($nameLower, $catLower) !== false) {
            return (int)$cat['id'];
        }
    }
    return (int)$categories[0]['id'];
}

function sppg_is_premium_material($materialName)
{
    $key = strtolower($materialName);
    $needles = [
        'ikan',
        'daging',
        'ayam',
        'sapi',
        'kambing',
        'iga',
        'keju',
        'mentega',
        'andaliman',
    ];
    foreach ($needles as $needle) {
        if (strpos($key, $needle) !== false) {
            return true;
        }
    }
    return false;
}

function sppg_generate_random_stock($materialName)
{
    $isPremium = sppg_is_premium_material($materialName);
    if ($isPremium) {
        $stock = mt_rand(1, 20);
        $minStock = max(1, (int)floor($stock * 0.2));
    } else {
        $stock = mt_rand(10, 100);
        $minStock = max(1, (int)floor($stock * 0.3));
    }
    return [
        'stock' => $stock,
        'min_stock' => $minStock,
        'is_premium' => $isPremium,
    ];
}

function sppg_estimate_buy_price($materialName, $unit)
{
    $key = strtolower($materialName);
    $basePrices = [
        'beras' => 14000.0,
        'minyak' => 17000.0,
        'gula' => 15000.0,
        'telur' => 28000.0,
        'bawang merah' => 32000.0,
        'bawang putih' => 28000.0,
        'ikan nila' => 35000.0,
        'daging ayam' => 35000.0,
        'ayam filet' => 45000.0,
        'tempe' => 8000.0,
        'tahu' => 8000.0,
        'garam kasar' => 8000.0,
        'garam halus' => 8000.0,
        'jeruk nipis' => 20000.0,
        'jeruk' => 18000.0,
        'melon' => 15000.0,
        'semangka' => 8000.0,
        'salak' => 15000.0,
        'kentang' => 15000.0,
        'wortel' => 14000.0,
        'buncis' => 14000.0,
        'pakcoy' => 15000.0,
        'kangkung' => 8000.0,
        'labu siam' => 8000.0,
        'sawi putih' => 10000.0,
        'duku' => 20000.0,
    ];
    foreach ($basePrices as $needle => $price) {
        if (strpos($key, $needle) !== false) {
            return $price;
        }
    }
    return 10000.0;
}

function sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup)
{
    $manualMapInvoice = sppg_invoice_manual_map();
    $unitMapInvoice = sppg_invoice_unit_map();
    foreach ($rowsInvoice as $row) {
        $nameKey = strtolower($row['name']);
        $code = null;
        foreach ($manualMapInvoice as $needle => $pcode) {
            if (strpos($nameKey, $needle) !== false) {
                $code = $pcode;
                break;
            }
        }
        if ($code === null) {
            continue;
        }
        $unitKey = strtolower($row['unit']);
        if (!isset($unitMapInvoice[$unitKey])) {
            continue;
        }
        $grams = (float)$row['qty'] * $unitMapInvoice[$unitKey];
        $beneficiariesCount = 0;
        $stmtDel = $conn->prepare("DELETE FROM sppg_daily_material_demand WHERE sppg_id = ? AND demand_date = ? AND material_code = ? AND target_group = ?");
        if ($stmtDel) {
            $stmtDel->bind_param('ssss', $sppgId, $demandDate, $code, $targetGroup);
            $stmtDel->execute();
            $stmtDel->close();
        }
        $stmtIns = $conn->prepare("INSERT INTO sppg_daily_material_demand (sppg_id, demand_date, material_code, target_group, beneficiaries_count, total_quantity_grams) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmtIns) {
            $stmtIns->bind_param('ssssid', $sppgId, $demandDate, $code, $targetGroup, $beneficiariesCount, $grams);
            $stmtIns->execute();
            $stmtIns->close();
        }
    }
}

function sppg_parse_pesanan_text($text)
{
    $lines = preg_split("/\\r\\n|\\n|\\r/", $text);
    $rows = [];
    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '') {
            continue;
        }
        $noSpace = str_replace(' ', '', $trim);
        if ($noSpace !== '' && preg_match('/^[=\\-]+$/', $noSpace)) {
            continue;
        }
        $partsTab = preg_split("/\\t+/", $line);
        if (count($partsTab) >= 3) {
            $partsTab = array_map('trim', $partsTab);
            $first = $partsTab[0] ?? '';
            if (preg_match('/^\\d+$/', $first)) {
                $no = (int)$first;
                $uraian = $partsTab[1] ?? '';
                $qtyRaw = $partsTab[2] ?? '';
                $satuan = $partsTab[3] ?? '';
                $extra = [];
                if (count($partsTab) > 4) {
                    $extra = array_slice($partsTab, 4);
                }
                $catatan = trim(implode(' ', $extra));
                $qty = 0.0;
                $qtyClean = preg_replace('/[^0-9,\\.]/', '', $qtyRaw);
                if ($qtyClean !== '') {
                    $qty = (float)str_replace(',', '.', $qtyClean);
                }
                $rows[] = [
                    'no' => $no,
                    'uraian' => $uraian,
                    'qty' => $qty,
                    'satuan' => $satuan,
                    'catatan' => $catatan,
                ];
                continue;
            }
        }
        if (preg_match('/^(?<uraian>.+?)\\s+(?<qty>[\\d.,]+)\\s+(?<satuan>\\S+)(?:\\s+(?<catatan>.+))?$/u', $trim, $m)) {
            $uraian = $m['uraian'];
            $qtyRaw = $m['qty'];
            $satuan = $m['satuan'];
            $catatan = isset($m['catatan']) ? $m['catatan'] : '';
            $qtyClean = preg_replace('/[^0-9,\\.]/', '', $qtyRaw);
            $qty = 0.0;
            if ($qtyClean !== '') {
                $qty = (float)str_replace(',', '.', $qtyClean);
            }
            $rows[] = [
                'no' => null,
                'uraian' => $uraian,
                'qty' => $qty,
                'satuan' => $satuan,
                'catatan' => $catatan,
            ];
        }
    }
    return $rows;
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'show_2025_orders') {
    $sqlSummary = "SELECT sppg_id, MIN(demand_date) AS min_date, MAX(demand_date) AS max_date, COUNT(*) AS row_count
                   FROM sppg_daily_material_demand
                   WHERE YEAR(demand_date) = 2025
                   GROUP BY sppg_id
                   ORDER BY sppg_id";
    $resSummary = $conn->query($sqlSummary);
    if ($resSummary) {
        echo "Ringkasan per SPPG (2025):" . PHP_EOL;
        while ($row = $resSummary->fetch_assoc()) {
            echo $row['sppg_id'] . " | baris: " . $row['row_count'] . " | min: " . $row['min_date'] . " | max: " . $row['max_date'] . PHP_EOL;
        }
        $resSummary->close();
    } else {
        echo "Error summary: " . $conn->error . PHP_EOL;
    }
    echo PHP_EOL . "Ringkasan harian (2025):" . PHP_EOL;
    $sqlDaily = "SELECT demand_date, sppg_id, COUNT(*) AS row_count, SUM(total_quantity_grams) / 1000 AS total_kg
                 FROM sppg_daily_material_demand
                 WHERE YEAR(demand_date) = 2025
                 GROUP BY demand_date, sppg_id
                 ORDER BY demand_date, sppg_id";
    $resDaily = $conn->query($sqlDaily);
    if ($resDaily) {
        while ($row = $resDaily->fetch_assoc()) {
            echo $row['demand_date'] . " | " . $row['sppg_id'] . " | baris: " . $row['row_count'] . " | total_kg: " . $row['total_kg'] . PHP_EOL;
        }
        $resDaily->close();
    } else {
        echo "Error daily: " . $conn->error . PHP_EOL;
    }
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_10') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-10';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Beras', 'qty' => 95, 'unit' => 'Kg'],
        ['name' => 'Ikan Nila', 'qty' => 150, 'unit' => 'kg'],
        ['name' => 'Garam Kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'Jeruk Nipis', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'Minyak', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'cabe rawit hijau', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'cabe rawit merah', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'jahe', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'kemiri', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'Tomat', 'qty' => 25, 'unit' => 'kg'],
        ['name' => 'Kincung/Kecombrang', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'andaliman', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'Tempe', 'qty' => 217, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'Pakcoy', 'qty' => 125, 'unit' => 'kg'],
        ['name' => 'Bawang Putih', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'Saus Tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam halus', 'qty' => 3, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'lada', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'tepung Maizena', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'Pisang', 'qty' => 108, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-10 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_12') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-12';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'Daun Jeruk', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'serai', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang putih bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'garam', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'Telur', 'qty' => 58, 'unit' => 'PAPAN'],
        ['name' => 'bawang merah', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'bawang ptih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 2.5, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun bawang', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'kecap manis', 'qty' => 17, 'unit' => 'pcs'],
        ['name' => 'saus tiram', 'qty' => 3, 'unit' => 'botol'],
        ['name' => 'garam halus', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'Tahu', 'qty' => 12, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'Kangkung', 'qty' => 120, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'terasi', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 0, 'unit' => 'PAK'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'Jeruk', 'qty' => 120, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-12 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_13') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-13';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'jagung', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'wortel', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'buncis', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'daun bawang', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'kecap manis', 'qty' => 17, 'unit' => 'pcs'],
        ['name' => 'lada', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'saus tiram', 'qty' => 2, 'unit' => 'Botol'],
        ['name' => 'cabe merah', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'ayam', 'qty' => 174, 'unit' => 'Kg'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'kunyit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'serai', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'jahe', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'lengkuas', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun jeruk', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'garam halus', 'qty' => 3, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 20, 'unit' => 'kg'],
        ['name' => 'Tahu', 'qty' => 12, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'jagung', 'qty' => 25, 'unit' => 'kg'],
        ['name' => 'wortel', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'buncis', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 3, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 0, 'unit' => 'bungkus'],
        ['name' => 'lada', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'salak', 'qty' => 75, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-13 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_14') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-14';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'ikan nila', 'qty' => 150, 'unit' => 'kg'],
        ['name' => 'bawang bombai', 'qty' => 7, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 2.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'tomat', 'qty' => 20, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 3, 'unit' => 'botol'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'daun bawang', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'tepung maizena', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'gula', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'minyak', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'Tempe', 'qty' => 217, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'sawi putih', 'qty' => 110, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'duku', 'qty' => 90, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-14 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_15') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-15';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Roti', 'qty' => 29, 'unit' => 'Dus'],
        ['name' => 'Telur', 'qty' => 58, 'unit' => 'papan'],
        ['name' => 'susu uht 115 mill', 'qty' => 495, 'unit' => 'pcs'],
        ['name' => 'susu uht 125 mill', 'qty' => 1220, 'unit' => 'pcs'],
        ['name' => 'pisang', 'qty' => 108, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-15 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_16') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-16';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'ayam', 'qty' => 174, 'unit' => 'Kg'],
        ['name' => 'bawang putih', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'tepung maizena', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'tepung terigu', 'qty' => 50, 'unit' => 'kg'],
        ['name' => 'kecap asin', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'bawang bombai', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'mentega', 'qty' => 8, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 5, 'unit' => 'Botol'],
        ['name' => 'kecap manis', 'qty' => 10, 'unit' => 'pcs'],
        ['name' => 'saus tomat', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'gula', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'lada bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 40, 'unit' => 'kg'],
        ['name' => 'kentang', 'qty' => 108, 'unit' => 'kg'],
        ['name' => 'jagung', 'qty' => 25, 'unit' => 'kg'],
        ['name' => 'wortel', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'buncis', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 0, 'unit' => 'botol'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'lada', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'duku', 'qty' => 90, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-16 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_17') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-17';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'ikan nila', 'qty' => 150, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe hijau', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'Tomat', 'qty' => 20, 'unit' => 'kg'],
        ['name' => 'gula', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'garam Kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'lada', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'serai', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun jeruk', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'minyak', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'Tahu', 'qty' => 12, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'sawi putih', 'qty' => 110, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'jeruk', 'qty' => 120, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-17 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_18') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-18';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'ayam', 'qty' => 174, 'unit' => 'Kg'],
        ['name' => 'serai', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bumbu racik rendang', 'qty' => 25, 'unit' => 'pcs'],
        ['name' => 'daun kunyit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'lengkuas', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'santan kental', 'qty' => 85, 'unit' => 'sachet'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'bawang merah', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'jahe', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'kunyit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'kemiri', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Minyak', 'qty' => 25, 'unit' => 'kg'],
        ['name' => 'Tempe', 'qty' => 217, 'unit' => 'papan'],
        ['name' => 'bawang merah', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'lengkuas', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'kecap manis', 'qty' => 15, 'unit' => 'pcs'],
        ['name' => 'saus tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 15, 'unit' => 'kg'],
        ['name' => 'Labu Siam', 'qty' => 110, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'semangka', 'qty' => 115, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-18 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_19') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-19';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'Daun Jeruk', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'serai', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang putih bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'Telur', 'qty' => 58, 'unit' => 'PAPAN'],
        ['name' => 'bawang merah', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'bawang ptih', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'Tomat', 'qty' => 25, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun bawang', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 2, 'unit' => 'BOTOL'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'tahu', 'qty' => 12, 'unit' => 'papan'],
        ['name' => 'bawang bombai', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang putih bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'daun bawang', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'kecap manis', 'qty' => 5, 'unit' => 'pcs'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'bumbu kaldu', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 15, 'unit' => 'kg'],
        ['name' => 'Timun', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'Tomat', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'salak', 'qty' => 75, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-19 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_20') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-20';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'beras', 'qty' => 95, 'unit' => 'kg'],
        ['name' => 'ikan nila', 'qty' => 150, 'unit' => 'kg'],
        ['name' => 'tomat', 'qty' => 15, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'kunyit', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'kemiri', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'ketumbar bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'gula', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'daun salam', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'lengkuas', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'jahe', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'minyak', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'Tempe', 'qty' => 217, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'Tauge', 'qty' => 75, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 2, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'pisang', 'qty' => 108, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-20 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2025_12_21') {
    $sppgId = 'SPPG_DEC2025_INVOICE';
    $demandDate = '2025-12-21';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Ubi cilembu', 'qty' => 115, 'unit' => 'kg'],
        ['name' => 'Telur', 'qty' => 58, 'unit' => 'papan'],
        ['name' => 'susu uht 115 mill', 'qty' => 495, 'unit' => 'pcs'],
        ['name' => 'susu uht 125 mill', 'qty' => 1220, 'unit' => 'pcs'],
        ['name' => 'jeruk', 'qty' => 120, 'unit' => 'kg'],
        ['name' => 'Plastik opp', 'qty' => 0, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2025-12-21 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2026_01_15') {
    $sppgId = 'SPPG_JAN2026_INVOICE';
    $demandDate = '2026-01-15';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Beras', 'qty' => 130, 'unit' => 'Kg'],
        ['name' => 'Ikan Nila', 'qty' => 260, 'unit' => 'kg'],
        ['name' => 'Garam Kasar', 'qty' => 3, 'unit' => 'PAK'],
        ['name' => 'Jeruk Nipis', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'Minyak', 'qty' => 65, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'cabe rawit hijau', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe rawit merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 8, 'unit' => 'kg'],
        ['name' => 'jahe', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'kemiri', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Tomat', 'qty' => 35, 'unit' => 'kg'],
        ['name' => 'batang rias', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'andaliman', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'garam halus', 'qty' => 5, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'Tempe', 'qty' => 297, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'Pakcoy', 'qty' => 170, 'unit' => 'kg'],
        ['name' => 'Bawang Putih', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'Saus Tiram', 'qty' => 4, 'unit' => 'botol'],
        ['name' => 'garam halus', 'qty' => 3, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'tepung Maizena', 'qty' => 5, 'unit' => 'kg'],
        ['name' => 'Minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'melon', 'qty' => 160, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2026-01-15 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2026_01_16') {
    $sppgId = 'SPPG_JAN2026_INVOICE';
    $demandDate = '2026-01-16';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Beras', 'qty' => 140, 'unit' => 'kg'],
        ['name' => 'Daun Jeruk', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'serai', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang putih bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'Telur', 'qty' => 78, 'unit' => 'PAPAN'],
        ['name' => 'bawang merah', 'qty' => 2.5, 'unit' => 'kg'],
        ['name' => 'bawang ptih', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'daun bawang', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'kecap manis', 'qty' => 27, 'unit' => 'pcs'],
        ['name' => 'saus tiram', 'qty' => 1, 'unit' => 'liter'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'gula pasir', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 65, 'unit' => 'kg'],
        ['name' => 'Tahu', 'qty' => 16.5, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 0, 'unit' => 'renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'renceng'],
        ['name' => 'Minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'Kangkung', 'qty' => 160, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'terasi', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 1, 'unit' => 'liter'],
        ['name' => 'garam', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'jeruk', 'qty' => 165, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2026-01-16 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2026_01_17') {
    $sppgId = 'SPPG_JAN2026_INVOICE';
    $demandDate = '2026-01-17';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'beras', 'qty' => 140, 'unit' => 'kg'],
        ['name' => 'ayam', 'qty' => 238, 'unit' => 'Kg'],
        ['name' => 'serai', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bumbu racik rendang', 'qty' => 50, 'unit' => 'pcs'],
        ['name' => 'daun kunyit', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'lengkuas', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'santan kental', 'qty' => 110, 'unit' => 'sachet'],
        ['name' => 'garam kasar', 'qty' => 1, 'unit' => 'PAK'],
        ['name' => 'kaldu bubuk', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'bawang merah', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 2, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'jahe', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'kunyit', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'kemiri', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'Minyak', 'qty' => 65, 'unit' => 'kg'],
        ['name' => 'Tempe', 'qty' => 300, 'unit' => 'papan'],
        ['name' => 'bawang merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'lengkuas', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'kecap manis', 'qty' => 25, 'unit' => 'pcs'],
        ['name' => 'saus tiram', 'qty' => 1, 'unit' => 'liter'],
        ['name' => 'garam halus', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'Labu Siam', 'qty' => 140, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 1, 'unit' => 'liter'],
        ['name' => 'garam halus', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'semangka', 'qty' => 155, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2026-01-17 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2026_01_18') {
    $sppgId = 'SPPG_JAN2026_INVOICE';
    $demandDate = '2026-01-18';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'beras', 'qty' => 130, 'unit' => 'kg'],
        ['name' => 'ikan nila', 'qty' => 250, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 3, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe hijau', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'Tomat hijau', 'qty' => 35, 'unit' => 'kg'],
        ['name' => 'gula', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'garam Kasar', 'qty' => 2, 'unit' => 'PAK'],
        ['name' => 'lada', 'qty' => 1, 'unit' => 'renteng'],
        ['name' => 'serai', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'daun jeruk', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'daun salam', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'minyak', 'qty' => 65, 'unit' => 'kg'],
        ['name' => 'Tahu', 'qty' => 16.5, 'unit' => 'papan'],
        ['name' => 'Tepung Terigu', 'qty' => 4, 'unit' => 'kg'],
        ['name' => 'Kunyit Bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'Ketumbar bubuk', 'qty' => 0, 'unit' => 'Renceng'],
        ['name' => 'Minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'sawi putih', 'qty' => 150, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'cabe merah', 'qty' => 1.5, 'unit' => 'kg'],
        ['name' => 'saos tiram', 'qty' => 1, 'unit' => 'liter'],
        ['name' => 'garam', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'salak', 'qty' => 102, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2026-01-18 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2026_01_19') {
    $sppgId = 'SPPG_JAN2026_INVOICE';
    $demandDate = '2026-01-19';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'ayam filet', 'qty' => 236, 'unit' => 'Kg'],
        ['name' => 'bawang putih', 'qty' => 6, 'unit' => 'kg'],
        ['name' => 'tepung maizena', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'tepung terigu', 'qty' => 65, 'unit' => 'kg'],
        ['name' => 'kecap asin', 'qty' => 4, 'unit' => 'botol'],
        ['name' => 'garam', 'qty' => 2, 'unit' => 'PAK'],
        ['name' => 'bawang bombai', 'qty' => 14, 'unit' => 'kg'],
        ['name' => 'mentega', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 2, 'unit' => 'liter'],
        ['name' => 'kecap manis', 'qty' => 15, 'unit' => 'pcs'],
        ['name' => 'saus tomat', 'qty' => 10, 'unit' => 'kg'],
        ['name' => 'gula', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'lada bubuk', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'kaldu bubuk', 'qty' => 1, 'unit' => 'bungkus'],
        ['name' => 'minyak', 'qty' => 65, 'unit' => 'kg'],
        ['name' => 'kentang', 'qty' => 147, 'unit' => 'kg'],
        ['name' => 'jagung', 'qty' => 30, 'unit' => 'kg'],
        ['name' => 'wortel', 'qty' => 35, 'unit' => 'kg'],
        ['name' => 'buncis', 'qty' => 35, 'unit' => 'kg'],
        ['name' => 'bawang merah', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'bawang putih', 'qty' => 1, 'unit' => 'kg'],
        ['name' => 'cabe rawit', 'qty' => 0.5, 'unit' => 'kg'],
        ['name' => 'saus tiram', 'qty' => 0, 'unit' => 'liter'],
        ['name' => 'garam halus', 'qty' => 2, 'unit' => 'bungkus'],
        ['name' => 'lada', 'qty' => 1, 'unit' => 'Renceng'],
        ['name' => 'minyak', 'qty' => 0, 'unit' => 'kg'],
        ['name' => 'duku', 'qty' => 120, 'unit' => 'kg'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2026-01-19 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'insert_invoice_2026_01_20') {
    $sppgId = 'SPPG_JAN2026_INVOICE';
    $demandDate = '2026-01-20';
    $targetGroup = 'anak';
    $rowsInvoice = [
        ['name' => 'Roti', 'qty' => 39, 'unit' => 'Dus'],
        ['name' => 'Telur', 'qty' => 78, 'unit' => 'papan'],
        ['name' => 'susu uht 115 mill', 'qty' => 729, 'unit' => 'pcs'],
        ['name' => 'susu uht 125 mill', 'qty' => 1605, 'unit' => 'pcs'],
        ['name' => 'pisang', 'qty' => 148, 'unit' => 'Sisir'],
    ];
    sppg_insert_invoice_rows($conn, $rowsInvoice, $sppgId, $demandDate, $targetGroup);
    echo 'Invoice 2026-01-20 inserted for ' . $sppgId . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'generate_products_from_demand') {
    $summary = [
        'total_materials' => 0,
        'candidates' => 0,
        'insert_target' => 0,
        'inserted' => 0,
        'skipped_existing' => 0,
        'failed' => [],
        'inserted_products' => [],
    ];

    $sqlMat = "SELECT d.material_code, sm.material_name, sm.unit, sm.category
FROM sppg_daily_material_demand d
JOIN sppg_materials sm ON sm.material_code = d.material_code
GROUP BY d.material_code, sm.material_name, sm.unit, sm.category";
    $resMat = $conn->query($sqlMat);
    if (!$resMat) {
        echo 'Gagal membaca sppg_daily_material_demand: ' . $conn->error . PHP_EOL;
        exit(1);
    }
    $materials = [];
    while ($row = $resMat->fetch_assoc()) {
        $summary['total_materials']++;
        $materials[] = $row;
    }
    $resMat->close();

    $existingCodes = [];
    $resProd = $conn->query("SELECT code FROM products");
    if ($resProd) {
        while ($row = $resProd->fetch_assoc()) {
            $code = $row['code'];
            if ($code !== '') {
                $existingCodes[$code] = true;
            }
        }
        $resProd->close();
    }

    $categories = [];
    $resCat = $conn->query("SELECT id, name FROM product_categories");
    if ($resCat) {
        while ($row = $resCat->fetch_assoc()) {
            $categories[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
            ];
        }
        $resCat->close();
    }

    $candidates = [];
    foreach ($materials as $row) {
        $code = $row['material_code'];
        $name = $row['material_name'];
        $unit = $row['unit'];
        if ($code === '' || $name === '' || $unit === '') {
            $summary['failed'][] = [
                'code' => $code,
                'reason' => 'field_wajib_kosong',
            ];
            continue;
        }
        if (isset($existingCodes[$code])) {
            $summary['skipped_existing']++;
            continue;
        }
        $categoryId = sppg_pick_category_id_for_material($name, $categories);
        $buyPrice = sppg_estimate_buy_price($name, $unit);
        if ($buyPrice <= 0) {
            $summary['failed'][] = [
                'code' => $code,
                'reason' => 'harga_beli_tidak_valid',
            ];
            continue;
        }
        $sellPrice = round($buyPrice * 1.2, 2);
        $stockInfo = sppg_generate_random_stock($name);
        $candidates[] = [
            'code' => $code,
            'name' => sppg_to_title_case($name),
            'unit' => $unit,
            'category_id' => $categoryId,
            'buy_price' => $buyPrice,
            'sell_price' => $sellPrice,
            'stock' => $stockInfo['stock'],
            'min_stock' => $stockInfo['min_stock'],
            'is_premium' => $stockInfo['is_premium'],
        ];
    }

    $summary['candidates'] = count($candidates);
    if ($summary['candidates'] === 0) {
        echo 'Tidak ada kandidat produk baru dari sppg_daily_material_demand.' . PHP_EOL;
        echo 'Total material unik: ' . $summary['total_materials'] . PHP_EOL;
        echo 'Sudah ada di products: ' . $summary['skipped_existing'] . PHP_EOL;
        if (!empty($summary['failed'])) {
            echo 'Gagal diproses: ' . count($summary['failed']) . PHP_EOL;
        }
        exit(0);
    }

    $minInsert = max(1, (int)ceil($summary['candidates'] * 0.5));
    $maxInsert = $summary['candidates'];
    $insertTarget = mt_rand($minInsert, $maxInsert);
    $summary['insert_target'] = $insertTarget;

    shuffle($candidates);
    $candidates = array_slice($candidates, 0, $insertTarget);

    $conn->begin_transaction();
    $sqlInsert = "INSERT INTO products (code, name, unit, barcode, category_id, buy_price, sell_price, is_active) VALUES (?, ?, ?, '', ?, ?, ?, ?)";
    $stmtIns = $conn->prepare($sqlInsert);
    if (!$stmtIns) {
        echo 'Gagal menyiapkan query insert produk: ' . $conn->error . PHP_EOL;
        $conn->rollback();
        exit(1);
    }

    $ok = true;
    $isActive = 1;
    foreach ($candidates as $row) {
        $code = $row['code'];
        $name = $row['name'];
        $unit = $row['unit'];
        $categoryId = (int)$row['category_id'];
        $buyPrice = (float)$row['buy_price'];
        $sellPrice = (float)$row['sell_price'];
        $stmtIns->bind_param('sssiddi', $code, $name, $unit, $categoryId, $buyPrice, $sellPrice, $isActive);
        if (!$stmtIns->execute()) {
            $ok = false;
            $summary['failed'][] = [
                'code' => $code,
                'reason' => 'insert_failed',
            ];
            break;
        }
        $summary['inserted']++;
        $summary['inserted_products'][] = [
            'code' => $code,
            'name' => $name,
            'unit' => $unit,
            'category_id' => $categoryId,
            'buy_price' => $buyPrice,
            'sell_price' => $sellPrice,
            'stock' => $row['stock'],
            'min_stock' => $row['min_stock'],
            'is_premium' => $row['is_premium'],
        ];
    }
    $stmtIns->close();

    if ($ok) {
        $conn->commit();
    } else {
        $conn->rollback();
        $summary['inserted'] = 0;
        $summary['inserted_products'] = [];
    }

    echo 'Total material unik di sppg_daily_material_demand: ' . $summary['total_materials'] . PHP_EOL;
    echo 'Kandidat produk baru: ' . $summary['candidates'] . PHP_EOL;
    echo 'Target produk yang akan disimpan (acak): ' . $summary['insert_target'] . PHP_EOL;
    echo 'Produk berhasil ditambahkan ke tabel products: ' . $summary['inserted'] . PHP_EOL;
    echo 'Produk dengan kode sudah ada di products (dilewati): ' . $summary['skipped_existing'] . PHP_EOL;
    if (!empty($summary['inserted_products'])) {
        echo PHP_EOL . 'Detail produk baru:' . PHP_EOL;
        foreach ($summary['inserted_products'] as $p) {
            $premiumFlag = $p['is_premium'] ? 'premium' : 'reguler';
            echo $p['code'] . ' | ' . $p['name'] . ' | ' . $p['unit'] .
                ' | kategori_id: ' . $p['category_id'] .
                ' | beli: ' . number_format($p['buy_price'], 2, '.', '') .
                ' | jual: ' . number_format($p['sell_price'], 2, '.', '') .
                ' | stok: ' . $p['stock'] .
                ' | stok_min: ' . $p['min_stock'] .
                ' | tipe: ' . $premiumFlag . PHP_EOL;
        }
    }
    if (!empty($summary['failed'])) {
        echo PHP_EOL . 'Produk yang gagal diproses:' . PHP_EOL;
        foreach ($summary['failed'] as $f) {
            echo ($f['code'] !== '' ? $f['code'] : '(tanpa_kode)') . ' | alasan: ' . $f['reason'] . PHP_EOL;
        }
    }
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'update_products_prices_and_stock') {
    $summary = [
        'total_products' => 0,
        'price_fixed' => 0,
        'stock_assigned' => 0,
    ];

    $resCol = $conn->query("SHOW COLUMNS FROM products LIKE 'stock_qty'");
    if (!$resCol || $resCol->num_rows === 0) {
        $sqlAlter = "ALTER TABLE products
ADD COLUMN stock_qty DECIMAL(15,3) NOT NULL DEFAULT 0 AFTER sell_price,
ADD COLUMN min_stock_qty DECIMAL(15,3) NOT NULL DEFAULT 0 AFTER stock_qty";
        if (!$conn->query($sqlAlter)) {
            echo 'Gagal menambah kolom stok pada tabel products: ' . $conn->error . PHP_EOL;
            exit(1);
        }
    }
    if ($resCol) {
        $resCol->close();
    }

    $sqlProd = "SELECT id, code, name, unit, buy_price, sell_price, stock_qty, min_stock_qty FROM products";
    $resProd = $conn->query($sqlProd);
    if (!$resProd) {
        echo 'Gagal membaca produk: ' . $conn->error . PHP_EOL;
        exit(1);
    }

    $products = [];
    while ($row = $resProd->fetch_assoc()) {
        $summary['total_products']++;
        $products[] = $row;
    }
    $resProd->close();

    if (empty($products)) {
        echo 'Tidak ada produk di tabel products.' . PHP_EOL;
        exit(0);
    }

    $conn->begin_transaction();
    $sqlUpdate = "UPDATE products SET buy_price = ?, sell_price = ?, stock_qty = ?, min_stock_qty = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    if (!$stmtUpdate) {
        echo 'Gagal menyiapkan query update produk: ' . $conn->error . PHP_EOL;
        $conn->rollback();
        exit(1);
    }

    $ok = true;
    foreach ($products as $row) {
        $id = (int)$row['id'];
        $name = $row['name'];
        $unit = $row['unit'];
        $buyPrice = (float)$row['buy_price'];
        $sellPrice = (float)$row['sell_price'];
        $stockQty = isset($row['stock_qty']) ? (float)$row['stock_qty'] : 0.0;
        $minStockQty = isset($row['min_stock_qty']) ? (float)$row['min_stock_qty'] : 0.0;

        $needPriceFix = ($buyPrice <= 0.0 || $sellPrice <= 0.0);
        $needStockAssign = ($stockQty <= 0.0 || $minStockQty <= 0.0);

        if (!$needPriceFix && !$needStockAssign) {
            continue;
        }

        if ($needPriceFix) {
            if ($buyPrice <= 0.0) {
                $buyPrice = sppg_estimate_buy_price($name, $unit);
            }
            if ($sellPrice <= 0.0) {
                $sellPrice = round($buyPrice * 1.2, 2);
            }
            $summary['price_fixed']++;
        }

        if ($needStockAssign) {
            $stockInfo = sppg_generate_random_stock($name);
            $stockQty = (float)$stockInfo['stock'];
            $minStockQty = (float)$stockInfo['min_stock'];
            $summary['stock_assigned']++;
        }

        $stmtUpdate->bind_param('ddddi', $buyPrice, $sellPrice, $stockQty, $minStockQty, $id);
        if (!$stmtUpdate->execute()) {
            $ok = false;
            echo 'Gagal mengupdate produk ID ' . $id . ': ' . $stmtUpdate->error . PHP_EOL;
            break;
        }
    }
    $stmtUpdate->close();

    if ($ok) {
        $conn->commit();
    } else {
        $conn->rollback();
        echo 'Perubahan dibatalkan karena ada kesalahan.' . PHP_EOL;
        exit(1);
    }

    echo 'Total produk di tabel products: ' . $summary['total_products'] . PHP_EOL;
    echo 'Produk yang harga beli/jual-nya diperbaiki: ' . $summary['price_fixed'] . PHP_EOL;
    echo 'Produk yang diberi nilai stok dan stok minimal: ' . $summary['stock_assigned'] . PHP_EOL;
    exit(0);
}

if (PHP_SAPI === 'cli' && isset($argv[1]) && $argv[1] === 'sync_products_from_demand_all') {
    $summary = [
        'total_material_codes' => 0,
        'missing_before' => 0,
        'inserted' => 0,
        'failed' => [],
    ];

    $sqlMat = "SELECT d.material_code, sm.material_name, sm.unit, sm.category
FROM sppg_daily_material_demand d
JOIN sppg_materials sm ON sm.material_code = d.material_code
GROUP BY d.material_code, sm.material_name, sm.unit, sm.category";
    $resMat = $conn->query($sqlMat);
    if (!$resMat) {
        echo 'Gagal membaca sppg_daily_material_demand: ' . $conn->error . PHP_EOL;
        exit(1);
    }
    $materials = [];
    while ($row = $resMat->fetch_assoc()) {
        $summary['total_material_codes']++;
        $materials[] = $row;
    }
    $resMat->close();

    if (empty($materials)) {
        echo 'Tidak ada material di sppg_daily_material_demand.' . PHP_EOL;
        exit(0);
    }

    $existingCodes = [];
    $resProd = $conn->query("SELECT code FROM products");
    if ($resProd) {
        while ($row = $resProd->fetch_assoc()) {
            $code = $row['code'];
            if ($code !== '') {
                $existingCodes[$code] = true;
            }
        }
        $resProd->close();
    }

    $categories = [];
    $resCat = $conn->query("SELECT id, name FROM product_categories");
    if ($resCat) {
        while ($row = $resCat->fetch_assoc()) {
            $categories[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
            ];
        }
        $resCat->close();
    }

    $missing = [];
    foreach ($materials as $row) {
        $code = $row['material_code'];
        $name = $row['material_name'];
        $unit = $row['unit'];
        if ($code === '' || $name === '' || $unit === '') {
            $summary['failed'][] = [
                'code' => $code,
                'reason' => 'field_wajib_kosong',
            ];
            continue;
        }
        if (isset($existingCodes[$code])) {
            continue;
        }
        $missing[] = $row;
    }

    $summary['missing_before'] = count($missing);
    if ($summary['missing_before'] === 0) {
        echo 'Semua material di sppg_daily_material_demand sudah memiliki produk di tabel products.' . PHP_EOL;
        echo 'Total material unik: ' . $summary['total_material_codes'] . PHP_EOL;
        exit(0);
    }

    $conn->begin_transaction();
    $sqlInsert = "INSERT INTO products (code, name, unit, barcode, category_id, buy_price, sell_price, stock_qty, min_stock_qty, is_active)
VALUES (?, ?, ?, '', ?, ?, ?, ?, ?, ?)";
    $stmtIns = $conn->prepare($sqlInsert);
    if (!$stmtIns) {
        echo 'Gagal menyiapkan query insert produk: ' . $conn->error . PHP_EOL;
        $conn->rollback();
        exit(1);
    }

    $ok = true;
    foreach ($missing as $row) {
        $code = $row['material_code'];
        $name = $row['material_name'];
        $unit = $row['unit'];
        $categoryId = sppg_pick_category_id_for_material($name, $categories);
        $buyPrice = sppg_estimate_buy_price($name, $unit);
        if ($buyPrice <= 0) {
            $summary['failed'][] = [
                'code' => $code,
                'reason' => 'harga_beli_tidak_valid',
            ];
            continue;
        }
        $sellPrice = round($buyPrice * 1.2, 2);
        $stockInfo = sppg_generate_random_stock($name);
        $stockQty = (float)$stockInfo['stock'];
        $minStockQty = (float)$stockInfo['min_stock'];
        $isActive = 1;
        $productName = sppg_to_title_case($name);

        $stmtIns->bind_param('sssiddddi', $code, $productName, $unit, $categoryId, $buyPrice, $sellPrice, $stockQty, $minStockQty, $isActive);
        if (!$stmtIns->execute()) {
            $ok = false;
            $summary['failed'][] = [
                'code' => $code,
                'reason' => 'insert_failed',
            ];
            continue;
        }
        $summary['inserted']++;
    }
    $stmtIns->close();

    if ($ok) {
        $conn->commit();
    } else {
        $conn->commit();
    }

    $missingAfter = $summary['missing_before'] - $summary['inserted'];
    echo 'Total material unik di sppg_daily_material_demand: ' . $summary['total_material_codes'] . PHP_EOL;
    echo 'Material yang belum punya produk sebelum sinkronisasi: ' . $summary['missing_before'] . PHP_EOL;
    echo 'Produk baru yang ditambahkan ke tabel products: ' . $summary['inserted'] . PHP_EOL;
    echo 'Perkiraan material yang masih belum punya produk (gagal/terlewat): ' . $missingAfter . PHP_EOL;
    if (!empty($summary['failed'])) {
        echo PHP_EOL . 'Material yang gagal dibuat produknya:' . PHP_EOL;
        foreach ($summary['failed'] as $f) {
            echo ($f['code'] !== '' ? $f['code'] : '(tanpa_kode)') . ' | alasan: ' . $f['reason'] . PHP_EOL;
        }
    }
    exit(0);
}

$file = __DIR__ . '/excel/SIKLUS MENU,NILAI GIZI, BAHAN 1 MINGGU (JANUARI 2026).xlsx';

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

$outFile = __DIR__ . '/output_bahan_minggu1.json';
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

$summaryFile = __DIR__ . '/output_bahan_minggu1_summary.json';
file_put_contents($summaryFile, json_encode(array_values($summary), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$sppgId = 'SPPG_JAN2026_MINGGU1';
$baseDate = '2026-01-15';
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

