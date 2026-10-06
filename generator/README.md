# Generator

Generator menggunakan pendekatan berikut:

```text
EQuran.id API v2
        ↓
Fetch data satu surah
        ↓
Normalisasi struktur data
        ↓
Render konten ke layout HTML/CSS
        ↓
Hitung tinggi konten dan pagination
        ↓
Preview setiap halaman
        ↓
Export halaman ke format .xtc
```

Tujuannya adalah menghasilkan file yang dioptimalkan langsung untuk Xteink X4 Pro, bukan mengonversi layout dari PDF atau dokumen cetak.

Prinsip implementasi: data ayat diperlakukan sebagai data terstruktur; teks Arab, transliterasi, dan terjemahan dipertahankan sebagai satu unit; pagination mengikuti tinggi konten aktual; layout memakai kontras tinggi untuk e-paper; preview digunakan sebagai tahap pemeriksaan; dan satu surah menghasilkan satu file `.xtc`.

## Menjalankan

Kebutuhan: PHP 8+ dan browser modern. Node.js hanya diperlukan untuk live reload saat mengembangkan.

```bash
cd generator
php -S 127.0.0.1:8000
```

Buka http://127.0.0.1:8000, pilih surah, lalu tekan **EXPORT** untuk mengunduh file `.xtc` surah tersebut.

Opsional, untuk live reload saat mengedit `index.php`:

```bash
npm install
npm run dev
```

### Menyimpan langsung ke folder

`simpan-xtc.php` menerima hasil export dan menulisnya ke folder `xtc/` di root repositori (atau ke folder dalam variabel lingkungan `XTC_DIR`). Dari console browser, setelah surah terbuka:

```js
await simpanXtcSekarang()
```

Endpoint ini menulis file ke disk; jalankan hanya di `127.0.0.1`, jangan dipasang di server publik.

## Berkas

| Berkas | Fungsi |
| --- | --- |
| `index.php` | Antarmuka, pengambilan data API, layout, pagination, validasi, dan encoder `.xtc` |
| `simpan-xtc.php` | Endpoint lokal untuk menyimpan hasil export ke disk |
| `vite.config.js`, `package.json` | Live reload saat pengembangan (opsional) |

## Format `.xtc`

Setiap halaman dirender pada kanvas 480 × 800 px dengan html2canvas, dikonversi menjadi bitmap 1-bit (ambang luminans 160) dalam blok `XTG`, lalu digabung menjadi satu kontainer `XTC` berisi tabel indeks halaman.

## Lisensi

Kode generator: [MIT](LICENSE). Lisensi ini tidak mencakup data Al-Qur'an, transliterasi, dan terjemahan yang diambil dari API; lihat [`../LICENSE.md`](../LICENSE.md) dan [`../ATTRIBUTION.md`](../ATTRIBUTION.md).
