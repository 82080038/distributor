<?php

function excel_column_index_from_ref($cellRef)
{
    if ($cellRef === '') {
        return null;
    }
    $letters = '';
    $len = strlen($cellRef);
    for ($i = 0; $i < $len; $i++) {
        $ch = $cellRef[$i];
        if ($ch >= 'A' && $ch <= 'Z') {
            $letters .= $ch;
        } elseif ($ch >= 'a' && $ch <= 'z') {
            $letters .= strtoupper($ch);
        } else {
            break;
        }
    }
    if ($letters === '') {
        return null;
    }
    $index = 0;
    $lettersLen = strlen($letters);
    for ($i = 0; $i < $lettersLen; $i++) {
        $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
    }
    return $index - 1;
}

function excel_list_sheets($filePath)
{
    $result = [];
    if (!is_file($filePath)) {
        return $result;
    }
    if (!class_exists('ZipArchive')) {
        return $result;
    }
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return $result;
    }
    $workbookXml = $zip->getFromName('xl/workbook.xml');
    $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');
    if ($workbookXml === false || $relsXml === false) {
        $zip->close();
        return $result;
    }
    $rels = [];
    $relsDoc = simplexml_load_string($relsXml);
    if ($relsDoc && isset($relsDoc->Relationship)) {
        foreach ($relsDoc->Relationship as $rel) {
            $id = isset($rel['Id']) ? (string)$rel['Id'] : '';
            $target = isset($rel['Target']) ? (string)$rel['Target'] : '';
            if ($id !== '' && $target !== '') {
                if (strpos($target, '/') === 0) {
                    $rels[$id] = 'xl' . $target;
                } else {
                    $rels[$id] = 'xl/' . $target;
                }
            }
        }
    }
    $wbDoc = simplexml_load_string($workbookXml);
    if ($wbDoc && isset($wbDoc->sheets) && isset($wbDoc->sheets->sheet)) {
        foreach ($wbDoc->sheets->sheet as $sheet) {
            $name = isset($sheet['name']) ? (string)$sheet['name'] : '';
            $rid = '';
            if (isset($sheet['r:id'])) {
                $rid = (string)$sheet['r:id'];
            } elseif (isset($sheet['id'])) {
                $rid = (string)$sheet['id'];
            }
            $path = isset($rels[$rid]) ? $rels[$rid] : '';
            if ($name !== '' && $path !== '') {
                $result[] = [
                    'name' => $name,
                    'path' => $path,
                ];
            }
        }
    }
    $zip->close();
    return $result;
}

function read_excel_sheet($filePath, $sheetEntryName, $maxRows = null)
{
    if (!is_file($filePath)) {
        return [];
    }
    if (!class_exists('ZipArchive')) {
        return [];
    }
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return [];
    }
    $sharedStrings = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $sx = simplexml_load_string($sharedXml);
        if ($sx && isset($sx->si)) {
            foreach ($sx->si as $si) {
                $text = '';
                if (isset($si->t)) {
                    $text = (string)$si->t;
                } elseif (isset($si->r)) {
                    foreach ($si->r as $run) {
                        $text .= (string)$run->t;
                    }
                }
                $sharedStrings[] = $text;
            }
        }
    }
    $sheetXml = $zip->getFromName($sheetEntryName);
    if ($sheetXml === false) {
        $zip->close();
        return [];
    }
    $sheet = simplexml_load_string($sheetXml);
    $rows = [];
    if ($sheet && isset($sheet->sheetData) && isset($sheet->sheetData->row)) {
        foreach ($sheet->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $c) {
                $ref = isset($c['r']) ? (string)$c['r'] : '';
                $colIndex = excel_column_index_from_ref($ref);
                $v = isset($c->v) ? (string)$c->v : '';
                $type = isset($c['t']) ? (string)$c['t'] : '';
                if ($type === 's') {
                    $idx = (int)$v;
                    $v = isset($sharedStrings[$idx]) ? $sharedStrings[$idx] : '';
                }
                if ($colIndex !== null) {
                    $rowData[$colIndex] = $v;
                } else {
                    $rowData[] = $v;
                }
            }
            if (!empty($rowData)) {
                ksort($rowData);
                $rows[] = array_values($rowData);
            }
            if ($maxRows !== null && count($rows) >= $maxRows) {
                break;
            }
        }
    }
    $zip->close();
    return $rows;
}

function read_excel_first_sheet($filePath, $maxRows = null)
{
    if (!is_file($filePath)) {
        return [];
    }
    if (!class_exists('ZipArchive')) {
        return [];
    }
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return [];
    }
    $sharedStrings = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $sx = simplexml_load_string($sharedXml);
        if ($sx && isset($sx->si)) {
            foreach ($sx->si as $si) {
                $text = '';
                if (isset($si->t)) {
                    $text = (string)$si->t;
                } elseif (isset($si->r)) {
                    foreach ($si->r as $run) {
                        $text .= (string)$run->t;
                    }
                }
                $sharedStrings[] = $text;
            }
        }
    }
    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($sheetXml === false) {
        $zip->close();
        return [];
    }
    $sheet = simplexml_load_string($sheetXml);
    $rows = [];
    if ($sheet && isset($sheet->sheetData) && isset($sheet->sheetData->row)) {
        foreach ($sheet->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $c) {
                $ref = isset($c['r']) ? (string)$c['r'] : '';
                $colIndex = excel_column_index_from_ref($ref);
                $v = isset($c->v) ? (string)$c->v : '';
                $type = isset($c['t']) ? (string)$c['t'] : '';
                if ($type === 's') {
                    $idx = (int)$v;
                    $v = isset($sharedStrings[$idx]) ? $sharedStrings[$idx] : '';
                }
                if ($colIndex !== null) {
                    $rowData[$colIndex] = $v;
                } else {
                    $rowData[] = $v;
                }
            }
            if (!empty($rowData)) {
                ksort($rowData);
                $rows[] = array_values($rowData);
            }
            if ($maxRows !== null && count($rows) >= $maxRows) {
                break;
            }
        }
    }
    $zip->close();
    return $rows;
}
