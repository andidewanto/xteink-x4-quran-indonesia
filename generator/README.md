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

Source code generator belum disertakan.
