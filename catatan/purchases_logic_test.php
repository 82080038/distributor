<?php

function validate_purchase_items(array $items)
{
    $error = '';
    $prepared = [];
    $total = 0.0;
    foreach ($items as $row) {
        $pid = isset($row['product_id']) ? (int)$row['product_id'] : 0;
        $qtyRaw = isset($row['qty']) ? (string)$row['qty'] : '';
        $priceRaw = isset($row['price']) ? (string)$row['price'] : '';
        $qty = (float)str_replace(',', '', $qtyRaw);
        $price = (float)str_replace(',', '', $priceRaw);
        if ($pid <= 0) {
            continue;
        }
        if ($qty <= 0) {
            $error = 'Jumlah pembelian harus lebih besar dari nol.';
            break;
        }
        if ($price < 0) {
            $error = 'Harga tidak boleh bernilai negatif.';
            break;
        }
        $subtotal = $qty * $price;
        $prepared[] = [
            'product_id' => $pid,
            'qty' => $qty,
            'price' => $price,
            'subtotal' => $subtotal,
        ];
        $total += $subtotal;
    }
    if ($error === '' && empty($prepared)) {
        $error = 'Minimal harus ada satu item pembelian yang valid.';
    }
    return [$error, $total, $prepared];
}

function run_test($name, array $items, $expectedError, $expectedTotal)
{
    list($error, $total,) = validate_purchase_items($items);
    $ok = true;
    if ($expectedError !== $error) {
        $ok = false;
        echo $name . ' ERROR mismatch: expected "' . $expectedError . '" got "' . $error . '"' . PHP_EOL;
    }
    if ($expectedError === '' && abs($expectedTotal - $total) > 0.0001) {
        $ok = false;
        echo $name . ' TOTAL mismatch: expected ' . $expectedTotal . ' got ' . $total . PHP_EOL;
    }
    if ($ok) {
        echo $name . ' OK' . PHP_EOL;
    }
}

run_test('single_valid_item', [
    ['product_id' => 1, 'qty' => '2', 'price' => '1000'],
], '', 2000.0);

run_test('multiple_valid_items', [
    ['product_id' => 1, 'qty' => '1.5', 'price' => '2000'],
    ['product_id' => 2, 'qty' => '3', 'price' => '500'],
], '', 1.5 * 2000 + 3 * 500);

run_test('invalid_qty', [
    ['product_id' => 1, 'qty' => '0', 'price' => '1000'],
], 'Jumlah pembelian harus lebih besar dari nol.', 0.0);

run_test('invalid_price', [
    ['product_id' => 1, 'qty' => '1', 'price' => '-1'],
], 'Harga tidak boleh bernilai negatif.', 0.0);

run_test('no_valid_items', [
    ['product_id' => 0, 'qty' => '1', 'price' => '1000'],
], 'Minimal harus ada satu item pembelian yang valid.', 0.0);

