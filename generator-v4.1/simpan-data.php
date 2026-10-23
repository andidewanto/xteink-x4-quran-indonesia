<?php

declare(strict_types=1);

const API = 'https://equran.id/api/v2';

function teksWaktuSalinan(int $waktu): string
{
    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $saat = (new DateTimeImmutable('@' . $waktu))->setTimezone(new DateTimeZone('Asia/Jakarta'));
    $nama = $bulan[(int) $saat->format('n') - 1];

    return $saat->format('j') . ' ' . $nama . ' ' . $saat->format('Y, H.i');
}

function dataApi(string $path, string|false $json): array
{
    if ($json === false || $json === '') {
        throw new RuntimeException('Gagal menghubungi ' . $path);
    }
    $body = json_decode($json, true);
    if (!is_array($body) || ($body['code'] ?? 0) !== 200 || !isset($body['data'])) {
        throw new RuntimeException('Jawaban API tidak valid untuk ' . $path);
    }
    return $body['data'];
}

/**
 * Ambil beberapa path API sekaligus (paralel), dengan timeout dan 2x coba ulang per path.
 * Hasil dikembalikan dalam urutan yang sama dengan $paths.
 */
function ambilApiParalel(array $paths): array
{
    $hasil = [];
    $sisa = $paths;
    for ($coba = 1; $sisa !== [] && $coba <= 3; $coba += 1) {
        $multi = curl_multi_init();
        $handle = [];
        foreach ($sisa as $kunci => $path) {
            $ch = curl_init(API . $path);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_ENCODING => '',
            ]);
            curl_multi_add_handle($multi, $ch);
            $handle[$kunci] = $ch;
        }
        do {
            $status = curl_multi_exec($multi, $aktif);
            if ($aktif) {
                curl_multi_select($multi, 1.0);
            }
        } while ($aktif && $status === CURLM_OK);

        $gagal = [];
        foreach ($handle as $kunci => $ch) {
            $kode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $json = $kode === 200 ? curl_multi_getcontent($ch) : false;
            curl_multi_remove_handle($multi, $ch);
            curl_close($ch);
            try {
                $hasil[$kunci] = dataApi($sisa[$kunci], $json ?? false);
            } catch (RuntimeException $e) {
                if ($coba === 3) {
                    curl_multi_close($multi);
                    throw $e;
                }
                $gagal[$kunci] = $sisa[$kunci];
            }
        }
        curl_multi_close($multi);
        $sisa = $gagal;
    }
    $urut = [];
    foreach (array_keys($paths) as $kunci) {
        $urut[$kunci] = $hasil[$kunci];
    }
    return $urut;
}

function jsonTeks(mixed $nilai): ?string
{
    if ($nilai === null || $nilai === false) {
        return null;
    }
    return json_encode($nilai, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
}

function teksAyat(string $nilai): string
{
    return trim(str_replace("\u{0091}", "\u{2018}", $nilai));
}

function kolomTabel(PDO $pdo, string $tabel): array
{
    $nama = [];
    foreach ($pdo->query('PRAGMA table_info(' . $tabel . ')') as $kolom) {
        $nama[] = $kolom['name'];
    }
    return $nama;
}

function muatLapisanLokal(string $berkas): array
{
    $lapisan = ['surat' => [], 'ayat' => []];
    if (!is_file($berkas)) {
        return $lapisan;
    }

    $pdo = new PDO('sqlite:' . $berkas, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $tabel = array_column(
        $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table'")->fetchAll(),
        'name'
    );
    $jagaSurat = ['deskripsi', 'deskripsi_inggris', 'deskripsi_jepang', 'nama_inggris', 'nama_jepang', 'nama_jepang_katakana'];
    $jagaAyat = ['teks_inggris', 'teks_jepang', 'teks_latin_inggris'];

    if (in_array('surat', $tabel, true)) {
        $ada = array_values(array_intersect($jagaSurat, kolomTabel($pdo, 'surat')));
        if ($ada !== []) {
            foreach ($pdo->query('SELECT nomor, ' . implode(', ', $ada) . ' FROM surat') as $baris) {
                $lapisan['surat'][(int) $baris['nomor']] = $baris;
            }
        }
    }
    if (in_array('ayat', $tabel, true)) {
        $ada = array_values(array_intersect($jagaAyat, kolomTabel($pdo, 'ayat')));
        if ($ada !== []) {
            foreach ($pdo->query('SELECT surat, nomor_ayat, ' . implode(', ', $ada) . ' FROM ayat') as $baris) {
                $lapisan['ayat'][(int) $baris['surat'] . ':' . (int) $baris['nomor_ayat']] = $baris;
            }
        }
    }

    return $lapisan;
}

function nilaiLokal(array $baris, string $kolom, string $cadangan = ''): string
{
    $nilai = $baris[$kolom] ?? null;
    if (!is_string($nilai) || $nilai === '') {
        return $cadangan;
    }
    return $nilai;
}

function sumberInformasi(): array
{
    return [
        'inggris' => "Qur'an data source: EQuran.id\nEnglish translation:\nSaheeh International",
        'jepang' => "クルアーンのデータ出典：EQuran.id\n日本語訳：Saeed Sato",
    ];
}

function muatInformasiLokal(string $berkas): array
{
    if (!is_file($berkas)) {
        return [];
    }
    $pdo = new PDO('sqlite:' . $berkas, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $ada = $pdo->query("SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = 'informasi'")->fetchColumn();
    if ($ada === false) {
        return [];
    }
    $hasil = [];
    foreach ($pdo->query('SELECT bahasa, sumber FROM informasi') as $baris) {
        if (is_string($baris['bahasa']) && is_string($baris['sumber']) && $baris['sumber'] !== '') {
            $hasil[$baris['bahasa']] = $baris['sumber'];
        }
    }
    return $hasil;
}

function simpanSalinanQuran(?callable $progres = null): array
{
    $dir = __DIR__ . '/data';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Folder data tidak bisa dibuat.');
    }

    // Cegah dua proses revalidasi menulis berkas .tmp yang sama bersamaan.
    $kunci = fopen($dir . '/.revalidasi.lock', 'c');
    if ($kunci === false || !flock($kunci, LOCK_EX | LOCK_NB)) {
        throw new RuntimeException('Revalidasi lain sedang berjalan.');
    }

    try {
        return simpanSalinanTerkunci($dir, $progres);
    } finally {
        flock($kunci, LOCK_UN);
        fclose($kunci);
    }
}

function simpanSalinanTerkunci(string $dir, ?callable $progres): array
{
    $tujuan = $dir . '/quran.sqlite';
    $sementara = $tujuan . '.tmp';
    if (is_file($sementara)) {
        unlink($sementara);
    }

    $pdo = new PDO('sqlite:' . $sementara);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('
        CREATE TABLE surat (
            nomor INTEGER PRIMARY KEY,
            nama TEXT NOT NULL,
            nama_latin TEXT NOT NULL,
            arti TEXT NOT NULL,
            nama_inggris TEXT NOT NULL DEFAULT \'\',
            nama_jepang TEXT NOT NULL DEFAULT \'\',
            nama_jepang_katakana TEXT NOT NULL DEFAULT \'\',
            jumlah_ayat INTEGER NOT NULL,
            tempat_turun TEXT NOT NULL,
            deskripsi TEXT NOT NULL,
            deskripsi_inggris TEXT NOT NULL DEFAULT \'\',
            deskripsi_jepang TEXT NOT NULL DEFAULT \'\',
            audio_full TEXT NOT NULL,
            surat_sebelumnya TEXT,
            surat_selanjutnya TEXT
        );
        CREATE TABLE ayat (
            surat INTEGER NOT NULL,
            nomor_ayat INTEGER NOT NULL,
            teks_arab TEXT NOT NULL,
            teks_latin TEXT NOT NULL,
            teks_latin_inggris TEXT NOT NULL DEFAULT \'\',
            teks_indonesia TEXT NOT NULL,
            teks_inggris TEXT NOT NULL DEFAULT \'\',
            teks_jepang TEXT NOT NULL DEFAULT \'\',
            audio TEXT NOT NULL,
            PRIMARY KEY (surat, nomor_ayat),
            FOREIGN KEY (surat) REFERENCES surat(nomor)
        );
        CREATE TABLE informasi (
            bahasa TEXT PRIMARY KEY,
            sumber TEXT NOT NULL
        );
    ');

    $simpanSurat = $pdo->prepare('
        INSERT INTO surat (
            nomor, nama, nama_latin, arti, nama_inggris, nama_jepang, nama_jepang_katakana,
            jumlah_ayat, tempat_turun, deskripsi, deskripsi_inggris, deskripsi_jepang,
            audio_full, surat_sebelumnya, surat_selanjutnya
        ) VALUES (
            :nomor, :nama, :nama_latin, :arti, :nama_inggris, :nama_jepang, :nama_jepang_katakana,
            :jumlah_ayat, :tempat_turun, :deskripsi, :deskripsi_inggris, :deskripsi_jepang,
            :audio_full, :surat_sebelumnya, :surat_selanjutnya
        )
    ');
    $simpanAyat = $pdo->prepare('
        INSERT INTO ayat (
            surat, nomor_ayat, teks_arab, teks_latin, teks_latin_inggris, teks_indonesia, teks_inggris, teks_jepang, audio
        ) VALUES (
            :surat, :nomor_ayat, :teks_arab, :teks_latin, :teks_latin_inggris, :teks_indonesia, :teks_inggris, :teks_jepang, :audio
        )
    ');
    $lokal = muatLapisanLokal($tujuan);
    $informasiLokal = muatInformasiLokal($tujuan);
    $simpanInformasi = $pdo->prepare('INSERT INTO informasi (bahasa, sumber) VALUES (:bahasa, :sumber)');

    $lapor = static function (array $event) use ($progres): void {
        if ($progres) {
            $progres($event);
        }
    };

    $pdo->beginTransaction();
    $jumlahAyat = 0;
    try {
        $lapor(['tahap' => 'unduh', 'selesai' => 0, 'total' => 114, 'ayat' => 0]);
        $paralel = 8;
        $semua = [];
        for ($awal = 1; $awal <= 114; $awal += $paralel) {
            $paths = [];
            for ($nomor = $awal; $nomor < $awal + $paralel && $nomor <= 114; $nomor += 1) {
                $paths[$nomor] = '/surat/' . $nomor;
            }
            $semua += ambilApiParalel($paths);
            $lapor(['tahap' => 'unduh', 'selesai' => count($semua), 'total' => 114, 'ayat' => 0]);
        }
        foreach ($semua as $nomor => $data) {
            $suratLokal = $lokal['surat'][(int) $data['nomor']] ?? [];
            $simpanSurat->execute([
                'nomor' => $data['nomor'],
                'nama' => $data['nama'],
                'nama_latin' => $data['namaLatin'],
                'arti' => $data['arti'],
                'nama_inggris' => nilaiLokal($suratLokal, 'nama_inggris'),
                'nama_jepang' => nilaiLokal($suratLokal, 'nama_jepang'),
                'nama_jepang_katakana' => nilaiLokal($suratLokal, 'nama_jepang_katakana'),
                'jumlah_ayat' => $data['jumlahAyat'],
                'tempat_turun' => $data['tempatTurun'],
                'deskripsi' => nilaiLokal($suratLokal, 'deskripsi', (string) $data['deskripsi']),
                'deskripsi_inggris' => nilaiLokal($suratLokal, 'deskripsi_inggris'),
                'deskripsi_jepang' => nilaiLokal($suratLokal, 'deskripsi_jepang'),
                'audio_full' => jsonTeks($data['audioFull'] ?? []),
                'surat_sebelumnya' => jsonTeks($data['suratSebelumnya'] ?? null),
                'surat_selanjutnya' => jsonTeks($data['suratSelanjutnya'] ?? null),
            ]);
            foreach ($data['ayat'] as $ayat) {
                $kunciAyat = (int) $data['nomor'] . ':' . (int) $ayat['nomorAyat'];
                $ayatLokal = $lokal['ayat'][$kunciAyat] ?? [];
                $simpanAyat->execute([
                    'surat' => $data['nomor'],
                    'nomor_ayat' => $ayat['nomorAyat'],
                    'teks_arab' => teksAyat((string) $ayat['teksArab']),
                    'teks_latin' => teksAyat((string) $ayat['teksLatin']),
                    'teks_latin_inggris' => nilaiLokal($ayatLokal, 'teks_latin_inggris'),
                    'teks_indonesia' => teksAyat((string) $ayat['teksIndonesia']),
                    'teks_inggris' => nilaiLokal($ayatLokal, 'teks_inggris'),
                    'teks_jepang' => nilaiLokal($ayatLokal, 'teks_jepang'),
                    'audio' => jsonTeks($ayat['audio'] ?? []),
                ]);
                $jumlahAyat += 1;
            }
        }
        foreach (sumberInformasi() as $bahasa => $sumber) {
            $simpanInformasi->execute([
                'bahasa' => $bahasa,
                'sumber' => $informasiLokal[$bahasa] ?? $sumber,
            ]);
        }
        $pdo->commit();
        $lapor(['tahap' => 'verifikasi', 'selesai' => 114, 'total' => 114, 'ayat' => $jumlahAyat]);
        $jumlahSurat = (int) $pdo->query('SELECT COUNT(*) FROM surat')->fetchColumn();
        $ayatTersimpan = (int) $pdo->query('SELECT COUNT(*) FROM ayat')->fetchColumn();
        $ayatTerdaftar = (int) $pdo->query('SELECT COALESCE(SUM(jumlah_ayat), 0) FROM surat')->fetchColumn();
        if ($jumlahSurat !== 114 || $ayatTersimpan !== 6236 || $ayatTerdaftar !== 6236 || $jumlahAyat !== 6236) {
            throw new RuntimeException('Salinan tidak valid: ' . $jumlahSurat . ' surat, ' . $ayatTersimpan . ' ayat.');
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $pdo = null;
        if (is_file($sementara)) {
            unlink($sementara);
        }
        throw $e;
    }

    $pdo = null;
    if (!rename($sementara, $tujuan)) {
        throw new RuntimeException('Gagal menaruh basis data.');
    }
    $waktu = filemtime($tujuan);
    if ($waktu === false) {
        $waktu = time();
    }

    return [
        'jumlahAyat' => $jumlahAyat,
        'diperbarui' => (new DateTimeImmutable('@' . $waktu))
            ->setTimezone(new DateTimeZone('Asia/Jakarta'))
            ->format(DateTimeInterface::ATOM),
        'teks' => teksWaktuSalinan($waktu),
    ];
}

if (PHP_SAPI === 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath((string) $_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    try {
        $hasil = simpanSalinanQuran(static function (array $event): void {
            if (($event['tahap'] ?? '') === 'unduh' && ($event['selesai'] ?? 0) > 0) {
                fwrite(STDOUT, "Menyimpan surat {$event['selesai']}/{$event['total']}\n");
            }
        });
        fwrite(STDOUT, "Selesai. {$hasil['jumlahAyat']} ayat tersimpan di data/quran.sqlite\n");
    } catch (Throwable $e) {
        fwrite(STDERR, $e->getMessage() . "\n");
        exit(1);
    }
}
