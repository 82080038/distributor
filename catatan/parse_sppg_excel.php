<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'excel_reader.php';

$dir = __DIR__ . DIRECTORY_SEPARATOR . 'excel';
$defaultFile = $dir . DIRECTORY_SEPARATOR . 'SIKLUS MENU 12 HARI, NILAI GIZI, BAHAN.xlsx';
$file = $defaultFile;

if (php_sapi_name() === 'cli') {
    global $argv;
    if (isset($argv[1]) && $argv[1] !== '') {
        $candidate = $dir . DIRECTORY_SEPARATOR . $argv[1];
        if (is_file($candidate)) {
            $file = $candidate;
        }
    }
} elseif (isset($_GET['file'])) {
    $candidate = basename($_GET['file']);
    $candidatePath = $dir . DIRECTORY_SEPARATOR . $candidate;
    if (is_file($candidatePath)) {
        $file = $candidatePath;
    }
}

if (php_sapi_name() === 'cli') {
    echo 'File: ' . $file . PHP_EOL;
    $sheets = excel_list_sheets($file);
    $allSheets = [];
    if (empty($sheets)) {
        $rows = read_excel_first_sheet($file, 40);
        echo '=== Sheet: sheet1 (xl/worksheets/sheet1.xml)' . PHP_EOL;
        foreach ($rows as $row) {
            echo implode(' | ', $row) . PHP_EOL;
        }
        echo PHP_EOL;
        $allSheets[] = [
            'name' => 'sheet1',
            'path' => 'xl/worksheets/sheet1.xml',
            'rows' => $rows,
        ];
    } else {
        foreach ($sheets as $sheet) {
            echo '=== Sheet: ' . $sheet['name'] . ' (' . $sheet['path'] . ')' . PHP_EOL;
            $rows = read_excel_sheet($file, $sheet['path'], 40);
            foreach ($rows as $row) {
                echo implode(' | ', $row) . PHP_EOL;
            }
            echo PHP_EOL;
            $allSheets[] = [
                'name' => $sheet['name'],
                'path' => $sheet['path'],
                'rows' => $rows,
            ];
        }
    }
    $base = basename($file);
    $safeBase = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $base);
    $outFile = __DIR__ . DIRECTORY_SEPARATOR . 'preview_' . $safeBase . '.json';
    file_put_contents($outFile, json_encode($allSheets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo 'Preview JSON: ' . $outFile . PHP_EOL;
    exit(0);
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Excel SPPG</title>
</head>
<body>
<h1>Preview Excel SPPG</h1>
<p>File: <?php echo htmlspecialchars(basename($file), ENT_QUOTES, 'UTF-8'); ?></p>
<?php
$sheets = excel_list_sheets($file);
foreach ($sheets as $sheet):
    $rows = read_excel_sheet($file, $sheet['path'], 40);
    ?>
    <h2><?php echo htmlspecialchars($sheet['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <table border="1" cellpadding="4" cellspacing="0">
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                    <td><?php echo htmlspecialchars($cell, ENT_QUOTES, 'UTF-8'); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>
</body>
</html>
