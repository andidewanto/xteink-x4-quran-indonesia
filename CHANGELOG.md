# Catatan Perubahan

## Generator v4.1 — 23 Oktober 2026

Source generator terbaru tersedia di [`generator-v4.1/`](generator-v4.1/).

- Menambah viewer dan ekspor terpisah untuk Xteink, EPUB, dan Kindle.
- Menambah ekspor seluruh Al-Qur'an untuk EPUB dan Kindle.
- EPUB fixed-layout menanam font agar tampilan konsisten.
- Kindle memakai reflowable EPUB 3.0 dan font native Kindle.
- Menambah cover serta daftar isi yang dapat dinavigasi pada Kindle.
- Menambah bahasa Indonesia, Inggris, dan Jepang.
- Menambah salinan data SQLite lokal dan revalidasi EQuran.id.
- Memperbaiki arah tanda kurung nomor ayat pada screenshot.
- Menambah pemisah antarayat dan kontrol pemotongan ayat Kindle.
- Hasil Kindle telah lolos EPUBCheck dan Kindle Previewer.

Versi ini adalah **source generator**, bukan pengganti otomatis untuk 114 file XTC siap pakai dalam rilis v1.0.

## Koleksi v1.0 — 6 Oktober 2026

Rilis awal berisi:

- 114 file `.xtc`, satu file per surah;
- ZIP lengkap untuk pemakaian langsung di Xteink X4 Pro;
- checksum SHA-256;
- source generator awal di [`generator/`](generator/).

Gunakan [rilis v1.0](https://github.com/andidewanto/xteink-x4-quran-indonesia/releases/tag/v1.0) bila hanya membutuhkan file XTC siap salin tanpa menjalankan generator.
