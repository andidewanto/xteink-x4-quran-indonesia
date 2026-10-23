<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$berkas = __DIR__ . '/data/quran.sqlite';
if (!is_file($berkas)) {
    http_response_code(503);
    echo json_encode([
        'code' => 503,
        'message' => 'Salinan lokal belum ada. Jalankan simpan-data.php sekali.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$pdo = new PDO('sqlite:' . $berkas, null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function suratJson(array $baris, bool $ringkas = false): array
{
    $surat = [
        'nomor' => (int) $baris['nomor'],
        'nama' => $baris['nama'],
        'namaLatin' => $baris['nama_latin'],
        'namaInggris' => $baris['nama_inggris'],
        'namaJepang' => $baris['nama_jepang'],
        'namaJepangKatakana' => $baris['nama_jepang_katakana'],
        'jumlahAyat' => (int) $baris['jumlah_ayat'],
        'tempatTurun' => $baris['tempat_turun'],
        'arti' => $baris['arti'],
        'deskripsi' => $baris['deskripsi'],
        'deskripsiInggris' => $baris['deskripsi_inggris'],
        'deskripsiJepang' => $baris['deskripsi_jepang'],
        'audioFull' => json_decode($baris['audio_full'], true) ?? [],
    ];
    if (!$ringkas) {
        $surat['suratSebelumnya'] = $baris['surat_sebelumnya'] === null
            ? false
            : json_decode($baris['surat_sebelumnya'], true);
        $surat['suratSelanjutnya'] = $baris['surat_selanjutnya'] === null
            ? false
            : json_decode($baris['surat_selanjutnya'], true);
    }
    return $surat;
}

$rute = $_GET['r'] ?? '/surat';
// ramping=1: hanya kolom yang dipakai index.php (tanpa audio).
$ramping = ($_GET['ramping'] ?? '') === '1';
$bahasa = $_GET['bahasa'] ?? 'id';
if (!in_array($bahasa, ['id', 'en', 'ja'], true)) {
    $bahasa = 'id';
}
$kodeJson = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

function suratRamping(array $baris, bool $denganNama = false): array
{
    $surat = [
        'nomor' => (int) $baris['nomor'],
        'nama' => $baris['nama'],
        'namaLatin' => $baris['nama_latin'],
        'jumlahAyat' => (int) $baris['jumlah_ayat'],
        'tempatTurun' => $baris['tempat_turun'],
        'arti' => $baris['arti'],
    ];
    if ($denganNama) {
        $surat['namaInggris'] = $baris['nama_inggris'];
        $surat['namaJepang'] = $baris['nama_jepang'];
        $surat['namaJepangKatakana'] = $baris['nama_jepang_katakana'];
    }
    return $surat;
}

function ayatJson(array $item, bool $ramping, string $bahasa): array
{
    $ayat = [
        'nomorAyat' => (int) $item['nomor_ayat'],
        'teksArab' => $item['teks_arab'],
        'teksLatin' => $item['teks_latin'],
        'teksIndonesia' => $item['teks_indonesia'],
    ];
    if (!$ramping || $bahasa === 'en') {
        $ayat['teksLatinInggris'] = $item['teks_latin_inggris'];
        $ayat['teksInggris'] = $item['teks_inggris'];
    }
    if (!$ramping || $bahasa === 'ja') {
        $ayat['teksJepang'] = $item['teks_jepang'];
    }
    if (!$ramping) {
        $ayat['audio'] = json_decode($item['audio'], true) ?? [];
    }
    return $ayat;
}

function deskripsiBahasa(array $baris, string $bahasa): string
{
    if ($bahasa === 'en') {
        return $baris['deskripsi_inggris'];
    }
    if ($bahasa === 'ja') {
        return $baris['deskripsi_jepang'];
    }
    return $baris['deskripsi'];
}

if ($rute === '/surat') {
    $baris = $pdo->query('SELECT * FROM surat ORDER BY nomor')->fetchAll();
    $data = array_map(
        static fn (array $item): array => $ramping ? suratRamping($item, true) : suratJson($item, true),
        $baris
    );
    echo json_encode(['code' => 200, 'data' => $data], $kodeJson);
    exit;
}

if ($rute === '/semua') {
    // Semua surat + ayat (ramping) dalam satu permintaan, untuk ekspor EPUB Qur'an penuh.
    $ayatPer = [];
    $kolom = 'surat, nomor_ayat, teks_arab, teks_latin, teks_indonesia';
    if ($bahasa === 'en') {
        $kolom .= ', teks_latin_inggris, teks_inggris';
    } elseif ($bahasa === 'ja') {
        $kolom .= ', teks_jepang';
    }
    foreach ($pdo->query('SELECT ' . $kolom . ' FROM ayat ORDER BY surat, nomor_ayat') as $item) {
        $ayatPer[(int) $item['surat']][] = ayatJson($item, true, $bahasa);
    }
    $data = [];
    foreach ($pdo->query('SELECT * FROM surat ORDER BY nomor') as $baris) {
        $surat = suratRamping($baris, true);
        $surat['ayat'] = $ayatPer[$surat['nomor']] ?? [];
        $data[] = $surat;
    }
    echo json_encode(['code' => 200, 'data' => $data], $kodeJson);
    exit;
}

if ($rute === '/informasi') {
    $baris = $pdo->query('SELECT bahasa, sumber FROM informasi ORDER BY bahasa')->fetchAll();
    $data = [];
    foreach ($baris as $item) {
        $data[$item['bahasa']] = $item['sumber'];
    }
    echo json_encode(['code' => 200, 'data' => $data], $kodeJson);
    exit;
}

if (preg_match('#^/surat/(\d+)$#', $rute, $cocok) === 1) {
    $nomor = (int) $cocok[1];
    $surat = $pdo->prepare('SELECT * FROM surat WHERE nomor = ?');
    $surat->execute([$nomor]);
    $baris = $surat->fetch();
    if (!$baris) {
        http_response_code(404);
        echo json_encode(['code' => 404, 'message' => 'Surat tidak ada.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $ayat = $pdo->prepare('SELECT * FROM ayat WHERE surat = ? ORDER BY nomor_ayat');
    $ayat->execute([$nomor]);
    $data = $ramping
        ? suratRamping($baris, true) + ['deskripsi' => deskripsiBahasa($baris, $bahasa)]
        : suratJson($baris);
    $data['ayat'] = array_map(
        static fn (array $item): array => ayatJson($item, $ramping, $bahasa),
        $ayat->fetchAll()
    );
    echo json_encode(['code' => 200, 'data' => $data], $kodeJson);
    exit;
}

http_response_code(404);
echo json_encode(['code' => 404, 'message' => 'Rute tidak dikenal.'], JSON_UNESCAPED_UNICODE);
