<?php

declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['code' => 405, 'message' => 'Gunakan POST.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Tolak POST dari situs lain (CSRF): hanya halaman di host yang sama boleh memicu revalidasi.
$asal = $_SERVER['HTTP_ORIGIN'] ?? '';
$situs = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? 'same-origin';
if (
    ($asal !== '' && parse_url($asal, PHP_URL_HOST) !== parse_url('//' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST))
    || !in_array($situs, ['same-origin', 'none'], true)
) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(403);
    echo json_encode(['code' => 403, 'message' => 'Permintaan lintas situs ditolak.'], JSON_UNESCAPED_UNICODE);
    exit;
}

set_time_limit(0);
ignore_user_abort(true);
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', '0');
header('Content-Type: application/x-ndjson; charset=utf-8');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
while (ob_get_level() > 0) {
    ob_end_flush();
}
ob_implicit_flush(true);

require_once __DIR__ . '/simpan-data.php';

function kirimProgres(array $event): void
{
    echo json_encode($event, JSON_UNESCAPED_UNICODE) . "\n";
    flush();
}

try {
    $hasil = simpanSalinanQuran(static function (array $event): void {
        kirimProgres($event);
    });
    kirimProgres([
        'code' => 200,
        'tahap' => 'selesai',
        'jumlahAyat' => $hasil['jumlahAyat'],
        'diperbarui' => $hasil['diperbarui'],
        'teks' => $hasil['teks'],
    ]);
} catch (Throwable $e) {
    kirimProgres([
        'code' => 502,
        'message' => $e->getMessage(),
    ]);
}
