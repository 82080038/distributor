<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed');
}
$conn->set_charset('utf8mb4');

$conn_alamat = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME_ALAMAT);
if ($conn_alamat->connect_error) {
    die('Alamat database connection failed');
}
$conn_alamat->set_charset('utf8mb4');

function clean($value)
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit();
}

function format_date_id($date)
{
    if ($date === null || $date === '') {
        return '';
    }
    $dateString = substr((string)$date, 0, 10);
    $dt = DateTime::createFromFormat('Y-m-d', $dateString);
    if ($dt === false) {
        return $dateString;
    }
    return $dt->format('d-m-Y');
}

function parse_date_id_to_db($value)
{
    if ($value === null) {
        return '';
    }
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }
    $formats = [
        ['d-m-Y H:i', 'Y-m-d H:i:s'],
        ['Y-m-d H:i', 'Y-m-d H:i:s'],
        ['Y-m-d H:i:s', 'Y-m-d H:i:s'],
        ['d-m-Y', 'Y-m-d'],
        ['Y-m-d', 'Y-m-d'],
    ];
    foreach ($formats as $pair) {
        $dt = DateTime::createFromFormat($pair[0], $value);
        if ($dt instanceof DateTime) {
            return $dt->format($pair[1]);
        }
    }
    return $value;
}

function number_to_indonesian_words($value)
{
    $num = (float)$value;
    if (!is_finite($num)) {
        return '';
    }
    $n = (int)floor($num);
    if ($n <= 0) {
        return 'Nol Rupiah';
    }
    $words = angka_to_kata_id($n);
    return trim($words) . ' Rupiah';
}

function angka_to_kata_id($x)
{
    $units = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas',
    ];
    if ($x < 12) {
        return $units[$x];
    }
    if ($x < 20) {
        return $units[$x - 10] . ' Belas';
    }
    if ($x < 100) {
        $tens = (int)floor($x / 10);
        $rest = $x % 10;
        $str = $units[$tens] . ' Puluh';
        if ($rest > 0) {
            $str .= ' ' . angka_to_kata_id($rest);
        }
        return $str;
    }
    if ($x < 200) {
        return 'Seratus' . ($x > 100 ? ' ' . angka_to_kata_id($x - 100) : '');
    }
    if ($x < 1000) {
        $hundreds = (int)floor($x / 100);
        $rest100 = $x % 100;
        $str = $units[$hundreds] . ' Ratus';
        if ($rest100 > 0) {
            $str .= ' ' . angka_to_kata_id($rest100);
        }
        return $str;
    }
    if ($x < 2000) {
        return 'Seribu' . ($x > 1000 ? ' ' . angka_to_kata_id($x - 1000) : '');
    }
    if ($x < 1000000) {
        $thousands = (int)floor($x / 1000);
        $rest1000 = $x % 1000;
        $str = angka_to_kata_id($thousands) . ' Ribu';
        if ($rest1000 > 0) {
            $str .= ' ' . angka_to_kata_id($rest1000);
        }
        return $str;
    }
    if ($x < 1000000000) {
        $millions = (int)floor($x / 1000000);
        $restMillion = $x % 1000000;
        $str = angka_to_kata_id($millions) . ' Juta';
        if ($restMillion > 0) {
            $str .= ' ' . angka_to_kata_id($restMillion);
        }
        return $str;
    }
    if ($x < 1000000000000) {
        $billions = (int)floor($x / 1000000000);
        $restBillion = $x % 1000000000;
        $str = angka_to_kata_id($billions) . ' Miliar';
        if ($restBillion > 0) {
            $str .= ' ' . angka_to_kata_id($restBillion);
        }
        return $str;
    }
    $trillions = (int)floor($x / 1000000000000);
    $restTrillion = $x % 1000000000000;
    $str = angka_to_kata_id($trillions) . ' Triliun';
    if ($restTrillion > 0) {
        $str .= ' ' . angka_to_kata_id($restTrillion);
    }
    return $str;
}

