# Generator v4.1

Versi terbaru generator Al-Qur'an dengan tiga target keluaran:

- **Xteink (`.xtc`)** — satu surah per file untuk Xteink X4 Pro.
- **EPUB** — fixed layout dengan font yang ditanam agar tampilan konsisten.
- **Kindle (`.epub`)** — reflowable EPUB 3.0, font native Kindle, cover, dan daftar isi.

Koleksi 114 file XTC siap pakai tetap tersedia pada [rilis v1.0](https://github.com/andidewanto/xteink-x4-quran-indonesia/releases/tag/v1.0). Gunakan generator ini bila ingin membuat ulang file, memilih bahasa, atau mengekspor EPUB/Kindle.

## Menjalankan

Kebutuhan:

- PHP 8+
- browser modern
- Node.js hanya untuk live reload saat mengembangkan

Jalankan server lokal dari folder ini:

```bash
php -S 127.0.0.1:8000
```

Buka `http://127.0.0.1:8000`, pilih format **xteink**, **EPUB**, atau **Kindle**, kemudian pilih surah atau ekspor seluruh Al-Qur'an.

Untuk live reload:

```bash
npm install
npm run dev
```

## Data dan aset

- `data/quran.sqlite` adalah salinan lokal data Al-Qur'an.
- `revalidasi.php` memperbarui salinan melalui EQuran.id API v2.
- `font/` dipakai oleh tampilan dan EPUB fixed-layout.
- Ekspor Kindle tidak menanam font kustom; perangkat memilih font native-nya.
- `gambar/halaman-1-epub.png` dipakai sebagai cover Kindle.

Jangan memasang endpoint penyimpanan atau revalidasi di server publik tanpa autentikasi. Generator ini ditujukan untuk penggunaan lokal.

## Perubahan utama dari generator v1.0

- pilihan antarmuka dan terjemahan Indonesia, Inggris, dan Jepang;
- salinan data SQLite lokal dan proses revalidasi;
- viewer terpisah untuk Xteink, EPUB, dan Kindle;
- ekspor EPUB fixed-layout dengan font tertanam;
- ekspor Kindle reflowable EPUB 3.0 dengan cover dan daftar isi;
- perbaikan arah tanda kurung nomor ayat pada screenshot;
- pemisah antarayat dan kontrol pemotongan ayat pada Kindle;
- ekspor Kindle telah diuji dengan EPUBCheck dan Kindle Previewer.

Riwayat ringkas tersedia di [`../CHANGELOG.md`](../CHANGELOG.md).

## Lisensi

Kode generator: [MIT](LICENSE). Lisensi kode tidak mencakup data Al-Qur'an, transliterasi, atau terjemahan. Lihat [`../LICENSE.md`](../LICENSE.md), [`../ATTRIBUTION.md`](../ATTRIBUTION.md), dan [`../DISCLAIMER.md`](../DISCLAIMER.md).
