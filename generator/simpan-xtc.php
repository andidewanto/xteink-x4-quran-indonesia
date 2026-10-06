<?php

declare(strict_types=1);

$nama = basename((string) ($_GET['nama'] ?? ''));
if (!preg_match('/^\d{3}-[\w.-]+\.xtc$/', $nama)) {
    http_response_code(400);
    echo 'nama tidak valid';
    exit;
}

$dir = getenv('XTC_DIR') ?: dirname(__DIR__) . '/xtc';
if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
    http_response_code(500);
    echo 'folder gagal dibuat';
    exit;
}

$tujuan = $dir . '/' . $nama;
$tambah = ($_GET['tambah'] ?? '0') === '1';
$masuk = fopen('php://input', 'rb');
$keluar = fopen($tujuan, $tambah ? 'ab' : 'wb');
if ($masuk === false || $keluar === false) {
    http_response_code(500);
    echo 'gagal menulis';
    exit;
}

stream_copy_to_stream($masuk, $keluar);
fclose($masuk);
fclose($keluar);

echo 'ok ' . filesize($tujuan);
