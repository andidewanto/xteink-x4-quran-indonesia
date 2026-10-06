<?php

declare(strict_types=1);

$judul = 'Quran';

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri+Quran&family=Lato:ital,wght@0,400;0,700;1,400&family=Noto+Sans+Arabic:wght@300;400;700&family=Roboto:ital,wght@0,400;0,500;0,700;1,400&family=Noto+Kufi+Arabic:wght@100..900&family=Scheherazade+New&display=swap" rel="stylesheet">
  <style>
    :root {
      color-scheme: dark;
      --md-primary: #8fd6b8;
      --md-on-primary: #003828;
      --md-primary-container: #1b5e4a;
      --md-on-primary-container: #c8efdc;
      --md-surface: #111311;
      --md-surface-container: #1c1f1d;
      --md-surface-container-high: #262a27;
      --md-on-surface: #e2e3e0;
      --md-on-surface-variant: #c1c9c3;
      --md-outline: #8b938c;
      --md-outline-variant: #3f4944;
      --md-error: #ffb4ab;
      --md-on-error: #690005;
      --space-1: 4px;
      --space-2: 8px;
      --space-3: 12px;
      --space-4: 16px;
      --space-5: 20px;
      --space-6: 24px;
      --space-8: 32px;
      --list-width: 360px;
      --readable: 720px;
      font-family: Roboto, system-ui, sans-serif;
      color: var(--md-on-surface);
      background: var(--md-surface);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
    }

    .app-bar {
      display: flex;
      align-items: center;
      gap: var(--space-4);
      min-height: 64px;
      padding: var(--space-3) var(--space-6);
      background: var(--md-surface);
      border-bottom: 1px solid var(--md-outline-variant);
    }

    .app-bar h1 {
      margin: 0;
      font-size: 1.375rem;
      font-weight: 500;
      letter-spacing: 0;
    }

    .app-bar p {
      margin: 0;
      color: var(--md-on-surface-variant);
      font-size: 0.875rem;
    }

    .layout {
      display: grid;
      grid-template-columns: minmax(280px, var(--list-width)) minmax(280px, 1fr) auto;
      align-items: stretch;
      height: calc(100vh - 64px);
    }

    .pane-list {
      border-right: 1px solid var(--md-outline-variant);
      background: var(--md-surface);
      display: flex;
      flex-direction: column;
      min-height: 0;
      overflow: hidden;
    }

    .search-wrap {
      padding: var(--space-4);
    }

    .validasi {
      margin: 0 var(--space-4) var(--space-3);
      font-size: 0.8125rem;
      line-height: 1.4;
    }

    .validasi.sah {
      color: var(--md-primary);
    }

    .validasi.gagal {
      color: var(--md-error);
    }

    .search-wrap label {
      display: block;
      margin-bottom: var(--space-2);
      font-size: 0.75rem;
      font-weight: 500;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: var(--md-on-surface-variant);
    }

    input[type="search"] {
      width: 100%;
      min-height: 48px;
      padding: 0 var(--space-4);
      border: 1px solid var(--md-outline);
      border-radius: 4px;
      background: var(--md-surface);
      color: var(--md-on-surface);
      font: inherit;
    }

    input:focus-visible,
    button:focus-visible {
      outline: 2px solid var(--md-primary);
      outline-offset: 2px;
    }

    .surat-list {
      list-style: none;
      margin: 0;
      padding: 0 var(--space-2) var(--space-4);
      overflow: auto;
      flex: 1;
      min-height: 0;
    }

    .surat-list button {
      width: 100%;
      display: grid;
      grid-template-columns: 40px minmax(0, 1fr);
      gap: var(--space-3);
      align-items: center;
      min-height: 64px;
      padding: var(--space-2) var(--space-3);
      border: 0;
      border-radius: 8px;
      background: transparent;
      color: inherit;
      text-align: start;
      font: inherit;
      cursor: pointer;
    }

    .surat-list button:hover {
      background: var(--md-surface-container);
    }

    .surat-list button[aria-current="true"] {
      background: var(--md-primary-container);
      color: var(--md-on-primary-container);
    }

    .nomor {
      width: 40px;
      height: 40px;
      display: grid;
      place-items: center;
      border-radius: 8px;
      background: var(--md-surface-container-high);
      font-size: 0.8125rem;
      font-weight: 500;
    }

    button[aria-current="true"] .nomor {
      background: var(--md-primary);
      color: var(--md-on-primary);
    }

    .nama {
      font-weight: 500;
    }

    .meta {
      display: block;
      margin-top: 2px;
      color: var(--md-on-surface-variant);
      font-size: 0.8125rem;
    }

    button[aria-current="true"] .meta {
      color: var(--md-on-primary-container);
    }

    .pane-detail {
      padding: var(--space-6);
      overflow: auto;
      min-height: 0;
      border-right: 1px solid var(--md-outline-variant);
    }

    .pane-page {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      padding: var(--space-6);
    }

    .layar-kolom {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: var(--space-3);
    }

    .screen {
      position: relative;
      width: calc(55.418mm * 1.32);
      height: calc(92.364mm * 1.32);
      overflow: hidden;
    }

    .screenshot-btn {
      min-height: 40px;
      border: 0;
      border-radius: 20px;
      background: var(--md-surface-container);
      color: var(--md-on-surface);
      font: inherit;
      font-size: 0.875rem;
      font-weight: 500;
      letter-spacing: 0.04em;
      cursor: pointer;
    }

    .screenshot-btn:disabled {
      opacity: 0.7;
      cursor: default;
    }

    .lompat {
      display: flex;
      align-items: center;
      gap: var(--space-2);
    }

    .lompat label {
      font-size: 0.8125rem;
      font-weight: 500;
      color: var(--md-on-surface-variant);
    }

    .lompat input {
      width: 100%;
      min-height: 40px;
      padding: 0 var(--space-4);
      border: 1px solid var(--md-outline);
      border-radius: 20px;
      background: var(--md-surface);
      color: var(--md-on-surface);
      font: inherit;
    }

    .lompat input:disabled {
      opacity: 0.7;
    }

    .page {
      position: absolute;
      top: 0;
      left: 0;
      width: 480px;
      height: 800px;
      overflow: hidden;
      transform: scale(calc(55.418mm * 1.32 / 480px));
      transform-origin: top left;
      color: #1a1a1a;
      background-color: #e4e2dc;
      background-image: radial-gradient(rgba(0, 0, 0, 0.045) 0.55px, transparent 0.55px);
      background-size: 3px 3px;
    }

    .page-body {
      height: 100%;
      padding: 24px 24px 52px;
      overflow: hidden;
    }

    .nomor-halaman {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 18px;
      margin: 0;
      text-align: center;
      font-family: Roboto, system-ui, sans-serif;
      font-size: 18px;
      font-weight: 500;
      line-height: 1;
      color: #1a1a1a;
    }

    .page-body.is-cover {
      padding: 0;
      background: #ffffff;
    }

    .page .teks-latin,
    .page .ayat-no,
    .page .status {
      color: #3a3a3a;
    }

    .cover {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
      background: #ffffff;
      color: #000000;
      text-align: right;
      font-family: "Noto Sans Arabic", sans-serif;
    }

    .cover p {
      position: absolute;
      right: 54px;
      margin: 0;
    }

    .cover-arab {
      top: 214px;
      font-family: "Noto Kufi Arabic", sans-serif;
      font-size: 90px;
      font-weight: 750;
      line-height: 96px;
      white-space: nowrap;
    }

    .cover-nama {
      top: 350px;
      font-size: 40px;
      font-weight: 700;
      line-height: 84px;
      white-space: nowrap;
    }

    .cover-arti {
      top: 400px;
      font-size: 32px;
      font-weight: 300;
      line-height: 68px;
      white-space: nowrap;
    }

    .cover-meta {
      top: 550px;
      font-size: 20px;
      font-weight: 700;
      line-height: 42px;
      white-space: nowrap;
    }

    .cover-sumber {
      top: 592px;
      right: 54px;
      left: 54px;
      font-size: 17px;
      font-weight: 400;
      line-height: 1.2;
      white-space: pre-line;
    }

    .chevron {
      width: 48px;
      height: 48px;
      flex: 0 0 48px;
      display: grid;
      place-items: center;
      padding: 0;
      border: 0;
      border-radius: 24px;
      background: var(--md-surface-container);
      color: var(--md-on-surface);
      cursor: pointer;
    }

    .chevron:hover:not(:disabled) {
      background: var(--md-primary-container);
      color: var(--md-on-primary-container);
    }

    .chevron:disabled {
      color: var(--md-outline-variant);
      cursor: default;
    }

    .detail-head {
      display: flex;
      flex-direction: column;
      gap: var(--space-2);
    }

    .deskripsi {
      margin: var(--space-6) 0 0;
      max-width: 62ch;
      line-height: 1.6;
    }

    .export-btn {
      align-self: flex-start;
      margin-top: var(--space-4);
      min-height: 40px;
      padding: 0 24px;
      border: 0;
      border-radius: 20px;
      background: var(--md-primary);
      color: var(--md-on-primary);
      font: inherit;
      font-size: 0.875rem;
      font-weight: 500;
      letter-spacing: 0.04em;
      cursor: pointer;
    }

    .export-btn:disabled {
      opacity: 0.7;
      cursor: default;
    }

    .page.is-export {
      position: relative;
      left: auto;
      top: auto;
      transform: none;
      background-color: #ffffff;
      background-image: none;
      color: #000000;
    }

    .page.is-export,
    .page.is-export * {
      color: #000000 !important;
    }

    .page.is-export .ayat {
      border-top-color: #000000;
    }

    .detail-head h2 {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 500;
    }

    .arab-title {
      margin: var(--space-1) 0 0;
      font-family: "Amiri Quran", serif;
      font-size: 1.75rem;
      line-height: 1.6;
    }

    .status {
      margin: 0;
      color: var(--md-on-surface-variant);
    }

    .status.error {
      color: var(--md-error);
    }

    .nama-surah {
      margin: 0 0 8px;
      font-family: Lato, sans-serif;
      font-size: 16px;
      line-height: 1.35;
      text-align: right;
      color: #1a1a1a;
    }

    .page-body.is-penutup {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }

    .penutup-arab {
      margin: 0;
      font-family: "Scheherazade New", serif;
      font-size: 46px;
      line-height: 2.1;
      text-align: center;
      direction: rtl;
    }

    .ayat {
      padding: var(--space-5) 0;
      border-top: 1px solid var(--md-outline-variant);
    }

    .ayat-top {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: var(--space-3);
      margin-bottom: 20px;
    }

    .ayat-no {
      font-size: 18px;
      font-weight: 500;
      color: var(--md-on-surface-variant);
    }

    .teks-arab {
      margin: 0 0 20px;
      font-family: "Scheherazade New", serif;
      font-size: 46px;
      line-height: 2.1;
      text-align: right;
      direction: rtl;
    }

    .teks-latin,
    .teks-id {
      margin: 0;
      font-family: Lato, sans-serif;
      font-size: 26px;
      line-height: 1.35;
      text-align: left;
    }

    .teks-latin {
      font-family: Lato, sans-serif;
      font-style: italic;
      font-weight: 400;
      color: #1a1a1a;
    }

    .teks-id {
      margin-top: 20px;
    }

  </style>
</head>
<body>
  <header class="app-bar">
    <div>
      <h1><?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') ?></h1>
      <p>Data dari equran.id API v2</p>
    </div>
  </header>
  <div class="layout">
    <aside class="pane-list">
      <div class="search-wrap">
        <label for="cari">Cari surat</label>
        <input id="cari" type="search" placeholder="Nama atau arti" autocomplete="off">
      </div>
      <p id="validasi" class="validasi" hidden></p>
      <p id="daftar-status" class="status" style="padding: 0 16px;">Memuat daftar surat…</p>
      <ul id="daftar" class="surat-list"></ul>
    </aside>
    <section id="detail" class="pane-detail" aria-label="Detail surat">
      <p class="status">Pilih surat untuk melihat detail.</p>
    </section>
    <section class="pane-page" aria-label="Halaman ayat">
      <button id="before" class="chevron" type="button" aria-label="Before" disabled>
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
          <path fill="currentColor" d="M15.4 7.4 14 6l-6 6 6 6 1.4-1.4L10.8 12z"/>
        </svg>
      </button>
      <div class="layar-kolom">
        <div class="screen">
          <div id="page" class="page">
            <div id="page-body" class="page-body">
              <p class="status">Pilih surat untuk memuat ayat.</p>
            </div>
          </div>
        </div>
        <form id="lompat-form" class="lompat">
          <label for="lompat">Ayat</label>
          <input id="lompat" type="number" min="1" step="1" inputmode="numeric" placeholder="Nomor" disabled>
        </form>
        <button id="screenshot" class="screenshot-btn" type="button" disabled>SCREENSHOT</button>
      </div>
      <button id="after" class="chevron" type="button" aria-label="After" disabled>
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
          <path fill="currentColor" d="M8.6 16.6 10 18l6-6-6-6-1.4 1.4L13.2 12z"/>
        </svg>
      </button>
    </section>
  </div>
  <script type="module" src="http://127.0.0.1:5173/@vite/client"></script>
  <script>
    const API = "https://equran.id/api/v2";

    const daftarEl = document.getElementById("daftar");
    const daftarStatus = document.getElementById("daftar-status");
    const detailEl = document.getElementById("detail");
    const pageBody = document.getElementById("page-body");
    const beforeBtn = document.getElementById("before");
    const afterBtn = document.getElementById("after");
    const screenshotBtn = document.getElementById("screenshot");
    const lompatForm = document.getElementById("lompat-form");
    const lompatEl = document.getElementById("lompat");
    const cariEl = document.getElementById("cari");
    const validasiEl = document.getElementById("validasi");
    const JUMLAH_AYAT_WAJIB = 6236;
    const suratGagal = new Map();

    let surat = [];
    let aktif = null;
    let ayatList = [];
    let suratData = null;
    let indeks = 0;
    let pecahan = new Map();
    let halamanCache = null;

    function angka(nilai) {
      return String(nilai).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function cekValidasi() {
      const total = surat.reduce((jumlah, item) => jumlah + Number(item.jumlahAyat || 0), 0);
      const nomor = surat.map((item) => Number(item.nomor)).sort((a, b) => a - b);
      const urut = nomor.length === 114 && nomor.every((n, i) => n === i + 1);
      const sah = urut && total === JUMLAH_AYAT_WAJIB && suratGagal.size === 0;
      validasiEl.hidden = false;
      validasiEl.className = sah ? "validasi sah" : "validasi gagal";
      if (sah) {
        validasiEl.textContent = "Valid · 114 surat · " + angka(total) + " ayat";
        return;
      }
      const alasan = [];
      if (!urut) {
        alasan.push(surat.length + " surat, harus 114");
      }
      if (total !== JUMLAH_AYAT_WAJIB) {
        alasan.push(angka(total) + " ayat, harus " + angka(JUMLAH_AYAT_WAJIB));
      }
      for (const pesan of suratGagal.values()) {
        alasan.push(pesan);
      }
      validasiEl.textContent = "Tidak valid · " + alasan.join(" · ");
    }

    function teks(value) {
      return String(value ?? "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
    }

    async function ambil(path) {
      const response = await fetch(API + path);
      if (!response.ok) {
        throw new Error("API menjawab " + response.status);
      }
      const body = await response.json();
      if (body.code !== 200) {
        throw new Error(body.message || "Permintaan gagal");
      }
      return body.data;
    }

    function tampilkanDaftar() {
      const kata = cariEl.value.trim().toLowerCase();
      const hasil = surat.filter((item) => {
        const gabungan = (item.namaLatin + " " + item.arti + " " + item.nomor).toLowerCase();
        return gabungan.includes(kata);
      });
      daftarEl.replaceChildren();
      if (!hasil.length) {
        daftarStatus.hidden = false;
        daftarStatus.className = "status";
        daftarStatus.textContent = "Tidak ada surat yang cocok.";
        return;
      }
      daftarStatus.hidden = true;
      for (const item of hasil) {
        const li = document.createElement("li");
        const button = document.createElement("button");
        button.type = "button";
        button.setAttribute("aria-current", String(aktif === item.nomor));
        button.innerHTML = "";
        const nomor = document.createElement("span");
        nomor.className = "nomor";
        nomor.textContent = String(item.nomor);
        const info = document.createElement("span");
        const nama = document.createElement("span");
        nama.className = "nama";
        nama.textContent = item.namaLatin;
        const meta = document.createElement("span");
        meta.className = "meta";
        meta.textContent = item.arti + " · " + item.jumlahAyat + " ayat · " + item.tempatTurun;
        info.append(nama, meta);
        button.append(nomor, info);
        button.addEventListener("click", () => bukaSurat(item.nomor));
        li.append(button);
        daftarEl.append(li);
      }
    }

    async function bukaSurat(nomor) {
      aktif = nomor;
      tampilkanDaftar();
      detailEl.replaceChildren();
      const menunggu = document.createElement("p");
      menunggu.className = "status";
      menunggu.textContent = "Memuat detail surat…";
      detailEl.append(menunggu);
      ayatList = [];
      suratData = null;
      screenshotBtn.disabled = true;
      lompatEl.disabled = true;
      lompatEl.value = "";
      indeks = 0;
      pecahan = new Map();
      halamanCache = null;
      pageBody.classList.remove("is-cover");
      pageBody.classList.remove("is-penutup");
      aturChevron();
      pageBody.replaceChildren();
      const status = document.createElement("p");
      status.className = "status";
      status.textContent = "Memuat ayat…";
      pageBody.append(status);
      try {
        const data = await ambil("/surat/" + nomor);
        gambarDetail(data);
      } catch (error) {
        const pesan = error.message || "Gagal memuat surat.";
        status.className = "status error";
        status.textContent = pesan;
        detailEl.replaceChildren();
        const gagal = document.createElement("p");
        gagal.className = "status error";
        gagal.textContent = pesan;
        detailEl.append(gagal);
      }
    }

    function gambarDetail(data) {
      detailEl.replaceChildren();
      const head = document.createElement("div");
      head.className = "detail-head";
      const judul = document.createElement("div");
      const h2 = document.createElement("h2");
      h2.textContent = data.nomor + ". " + data.namaLatin;
      const arab = document.createElement("p");
      arab.className = "arab-title";
      arab.lang = "ar";
      arab.dir = "rtl";
      arab.textContent = data.nama;
      const ringkas = document.createElement("p");
      ringkas.className = "meta";
      ringkas.textContent = data.arti + " · " + data.jumlahAyat + " ayat · " + data.tempatTurun;
      const deskripsi = document.createElement("p");
      deskripsi.className = "deskripsi";
      deskripsi.textContent = teks(data.deskripsi);
      judul.append(h2, arab, ringkas);
      const exportBtn = document.createElement("button");
      exportBtn.type = "button";
      exportBtn.className = "export-btn";
      exportBtn.textContent = "EXPORT";
      exportBtn.addEventListener("click", () => eksporXtc(exportBtn));
      head.append(judul, deskripsi, exportBtn);
      detailEl.append(head);
      suratData = data;
      ayatList = data.ayat || [];
      if (ayatList.length !== Number(data.jumlahAyat)) {
        suratGagal.set(data.nomor, data.namaLatin + " " + ayatList.length + "/" + data.jumlahAyat + " ayat");
      } else {
        suratGagal.delete(data.nomor);
      }
      cekValidasi();
      pecahan = new Map();
      halamanCache = null;
      indeks = 0;
      screenshotBtn.disabled = false;
      lompatEl.disabled = false;
      lompatEl.max = data.jumlahAyat;
      lompatEl.value = "";
      tampilkanHalaman();
    }

    function infoAyat(ayat) {
      let info = pecahan.get(ayat.nomorAyat);
      if (!info) {
        info = { susun: "penuh", arab: null, latin: null, id: null };
        pecahan.set(ayat.nomorAyat, info);
      }
      return info;
    }

    function daftarMentah() {
      const list = [{ jenis: "cover" }];
      for (const ayat of ayatList) {
        const info = pecahan.get(ayat.nomorAyat);
        const susun = info ? info.susun : "penuh";
        if (susun === "penuh") {
          list.push({ jenis: "penuh", ayat });
          continue;
        }
        if (info.arab) {
          for (const cuplikan of info.arab) {
            list.push({ jenis: "arab", ayat, cuplikan });
          }
        } else {
          list.push({ jenis: "arab", ayat });
        }
        if (susun === "tiga" || info.id) {
          if (info.latin) {
            for (const cuplikan of info.latin) {
              list.push({ jenis: "latin", ayat, cuplikan });
            }
          } else {
            list.push({ jenis: "latin", ayat });
          }
          if (info.id) {
            for (const cuplikan of info.id) {
              list.push({ jenis: "id", ayat, cuplikan });
            }
          } else {
            list.push({ jenis: "id", ayat });
          }
        } else {
          list.push({ jenis: "makna", ayat });
        }
      }
      return list;
    }

    function rapikanSemua() {
      for (const ayat of ayatList) {
        const info = infoAyat(ayat);
        if (info.susun === "penuh") {
          gambarAyat(ayat, "penuh");
          if (tidakMuat()) {
            info.susun = "makna";
          }
        }
        if (info.susun === "makna" && !info.arab) {
          gambarAyat(ayat, "arab");
          if (tidakMuat()) {
            const bagian = pecahBlok(ayat, "arab", teks(ayat.teksArab));
            if (bagian.length > 1) {
              info.arab = bagian;
            }
          }
        }
        if (info.susun !== "penuh" && info.susun !== "tiga" && !info.id) {
          gambarAyat(ayat, "makna");
          if (tidakMuat()) {
            info.susun = "tiga";
          }
        }
        if ((info.susun === "tiga" || info.id) && !info.latin) {
          gambarAyat(ayat, "latin");
          if (tidakMuat()) {
            const bagian = pecahBlok(ayat, "latin", teks(ayat.teksLatin));
            if (bagian.length > 1) {
              info.latin = bagian;
            }
          }
        }
        if ((info.susun === "tiga" || info.id) && !info.id) {
          gambarAyat(ayat, "id");
          if (tidakMuat()) {
            const bagian = pecahBlok(ayat, "id", teks(ayat.teksIndonesia));
            if (bagian.length > 1) {
              info.id = bagian;
            }
          }
        }
      }
    }

    function kemasHalaman(mentah) {
      const list = [];
      let i = 0;
      while (i < mentah.length) {
        const item = mentah[i];
        if (!item || item.jenis !== "penuh") {
          list.push(item);
          i += 1;
          continue;
        }
        const bagian = [item];
        while (bagian.length < 3 && i + bagian.length < mentah.length && mentah[i + bagian.length].jenis === "penuh") {
          gambarBagian(bagian.concat(mentah[i + bagian.length]));
          if (tidakMuat()) {
            break;
          }
          bagian.push(mentah[i + bagian.length]);
        }
        list.push(bagian.length === 1 ? item : { jenis: "grup", bagian });
        i += bagian.length;
      }
      return list;
    }

    function daftarHalaman() {
      if (!halamanCache) {
        rapikanSemua();
        halamanCache = kemasHalaman(daftarMentah()).concat([{ jenis: "penutup" }]);
      }
      return halamanCache;
    }

    function tidakMuat() {
      return pageBody.scrollHeight > pageBody.clientHeight + 1;
    }

    function aturChevron(list) {
      beforeBtn.disabled = indeks <= 0;
      afterBtn.disabled = !suratData || !list || indeks >= list.length - 1;
    }

    function tampilkanHalaman() {
      const list = daftarHalaman();
      if (indeks > list.length - 1) {
        indeks = Math.max(0, list.length - 1);
      }
      aturChevron(list);
      pageBody.replaceChildren();
      const item = list[indeks];
      const cover = item && item.jenis === "cover";
      pageBody.classList.toggle("is-cover", Boolean(cover));
      pageBody.classList.toggle("is-penutup", item && item.jenis === "penutup");
      if (!item) {
        return;
      }
      if (cover) {
        tampilkanCover();
        tulisNomorHalaman(list);
        return;
      }
      if (item.jenis === "grup") {
        gambarBagian(item.bagian);
        tulisNomorHalaman(list);
        return;
      }
      if (item.jenis === "penutup") {
        gambarPenutup();
        tulisNomorHalaman(list);
        return;
      }
      gambarAyat(item.ayat, item.jenis, item.cuplikan);
      if (!tidakMuat() || item.cuplikan) {
        tulisNomorHalaman(list);
        return;
      }
      const info = infoAyat(item.ayat);
      if (item.jenis === "penuh") {
        info.susun = "makna";
        halamanCache = null;
        tampilkanHalaman();
        return;
      }
      if (item.jenis === "arab") {
        const bagian = pecahBlok(item.ayat, "arab", teks(item.ayat.teksArab));
        if (bagian.length > 1) {
          info.arab = bagian;
          halamanCache = null;
          tampilkanHalaman();
          return;
        }
        tulisNomorHalaman(list);
        return;
      }
      if (item.jenis === "makna") {
        info.susun = "tiga";
        halamanCache = null;
        tampilkanHalaman();
        return;
      }
      if (item.jenis === "id") {
        const bagian = pecahBlok(item.ayat, "id", teks(item.ayat.teksIndonesia));
        if (bagian.length > 1) {
          info.id = bagian;
          halamanCache = null;
          tampilkanHalaman();
          return;
        }
      }
      if (item.jenis === "latin") {
        const bagian = pecahBlok(item.ayat, "latin", teks(item.ayat.teksLatin));
        if (bagian.length > 1) {
          info.latin = bagian;
          halamanCache = null;
          tampilkanHalaman();
          return;
        }
      }
      tulisNomorHalaman(list);
    }

    function tulisNomorHalaman(list) {
      if (indeks === 0) {
        return;
      }
      const nomor = document.createElement("p");
      nomor.className = "nomor-halaman";
      nomor.textContent = indeks + " / " + (list.length - 1);
      pageBody.append(nomor);
    }

    function pecahBlok(ayat, jenis, sumber) {
      const hasil = [];
      let sisa = sumber.trim().split(/\s+/).filter(Boolean);
      let lanjutan = false;
      let pengaman = 0;
      while (sisa.length && pengaman < 100) {
        pengaman += 1;
        let terbaik = 0;
        let low = 1;
        let high = sisa.length;
        while (low <= high) {
          const mid = Math.floor((low + high) / 2);
          const habis = mid === sisa.length;
          let calon = sisa.slice(0, mid).join(" ");
          if (lanjutan) {
            calon = "... " + calon;
          }
          if (!habis) {
            calon += " ...";
          }
          gambarAyat(ayat, jenis, calon);
          if (!tidakMuat()) {
            terbaik = mid;
            low = mid + 1;
          } else {
            high = mid - 1;
          }
        }
        if (terbaik === 0) {
          let calon = sisa.join(" ");
          if (lanjutan) {
            calon = "... " + calon;
          }
          hasil.push(calon);
          break;
        }
        const habis = terbaik === sisa.length;
        let calon = sisa.slice(0, terbaik).join(" ");
        if (lanjutan) {
          calon = "... " + calon;
        }
        if (!habis) {
          calon += " ...";
        }
        hasil.push(calon);
        if (habis) {
          break;
        }
        sisa = sisa.slice(terbaik);
        lanjutan = true;
      }
      return hasil;
    }

    function tampilkanCover() {
      const cover = document.createElement("article");
      cover.className = "cover";
      const arab = document.createElement("p");
      arab.className = "cover-arab";
      arab.lang = "ar";
      arab.dir = "rtl";
      arab.textContent = suratData.nama;
      const nama = document.createElement("p");
      nama.className = "cover-nama";
      nama.textContent = suratData.nomor + ": " + suratData.namaLatin;
      const arti = document.createElement("p");
      arti.className = "cover-arti";
      arti.textContent = suratData.arti;
      const meta = document.createElement("p");
      meta.className = "cover-meta";
      meta.textContent = suratData.jumlahAyat + " ayat · " + suratData.tempatTurun;
      const sumber = document.createElement("p");
      sumber.className = "cover-sumber";
      sumber.textContent = "Sumber Mushaf Al-Quran\nKementerian Agama Republik Indonesia\nGenerate menggunakan API dari equran.id\nv 1.0 - 2026";
      cover.append(arab, nama, arti, meta, sumber);
      pageBody.append(cover);
    }

    function buatBagian(ayat, jenis, cuplikan) {
      const section = document.createElement("article");
      section.className = "ayat";
      const top = document.createElement("div");
      top.className = "ayat-top";
      const no = document.createElement("span");
      no.className = "ayat-no";
      no.textContent = "Ayat " + ayat.nomorAyat + "/" + suratData.jumlahAyat;
      top.append(no);
      section.append(top);
      if (jenis === "penuh" || jenis === "arab") {
        const arab = document.createElement("p");
        arab.className = "teks-arab";
        arab.lang = "ar";
        arab.textContent = cuplikan || teks(ayat.teksArab);
        section.append(arab);
      }
      if (jenis === "penuh" || jenis === "makna" || jenis === "latin") {
        const latin = document.createElement("p");
        latin.className = "teks-latin";
        latin.textContent = (jenis === "latin" && cuplikan) || teks(ayat.teksLatin);
        section.append(latin);
      }
      if (jenis === "penuh" || jenis === "makna" || jenis === "id") {
        const indonesia = document.createElement("p");
        indonesia.className = "teks-id";
        indonesia.textContent = cuplikan || teks(ayat.teksIndonesia);
        section.append(indonesia);
      }
      return section;
    }

    function gambarBagian(bagian) {
      pageBody.replaceChildren();
      pageBody.classList.remove("is-cover");
      pageBody.classList.remove("is-penutup");
      const judul = document.createElement("p");
      judul.className = "nama-surah";
      judul.textContent = suratData.namaLatin;
      pageBody.append(judul);
      for (const item of bagian) {
        pageBody.append(buatBagian(item.ayat, item.jenis, item.cuplikan));
      }
    }

    function gambarPenutup() {
      pageBody.replaceChildren();
      pageBody.classList.remove("is-cover");
      pageBody.classList.add("is-penutup");
      const arab = document.createElement("p");
      arab.className = "penutup-arab";
      arab.lang = "ar";
      arab.dir = "rtl";
      arab.textContent = "صَدَقَ اللهُ اْلعَظِيْمُ";
      pageBody.append(arab);
    }

    function gambarAyat(ayat, jenis, cuplikan) {
      if (!ayat) {
        return;
      }
      gambarBagian([{ ayat, jenis, cuplikan }]);
    }

    let kanvasSiap = null;

    function muatKanvas() {
      if (window.html2canvas) {
        return Promise.resolve(window.html2canvas);
      }
      if (!kanvasSiap) {
        kanvasSiap = new Promise((resolve, reject) => {
          const skrip = document.createElement("script");
          skrip.src = "https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js";
          skrip.onload = () => resolve(window.html2canvas);
          skrip.onerror = () => reject(new Error("Gagal memuat renderer."));
          document.head.append(skrip);
        });
      }
      return kanvasSiap;
    }

    async function gambarLayar() {
      const render = await muatKanvas();
      const stage = document.getElementById("page").cloneNode(true);
      stage.classList.add("is-export");
      stage.removeAttribute("id");
      stage.querySelectorAll("[id]").forEach((el) => el.removeAttribute("id"));
      stage.style.position = "fixed";
      stage.style.left = "-2000px";
      stage.style.top = "0";
      stage.style.transform = "none";
      document.body.append(stage);
      try {
        const canvas = await render(stage, {
          width: 480,
          height: 800,
          scale: 1,
          backgroundColor: "#ffffff",
          logging: false,
        });
        return canvas.getContext("2d", { willReadFrequently: true }).getImageData(0, 0, 480, 800);
      } finally {
        stage.remove();
      }
    }

    function bitmapXtg(gambar) {
      const lebar = 480;
      const tinggi = 800;
      const byteBaris = lebar / 8;
      const data = new Uint8Array(byteBaris * tinggi);
      const px = gambar.data;
      for (let y = 0; y < tinggi; y += 1) {
        for (let x = 0; x < lebar; x += 1) {
          const i = (y * lebar + x) * 4;
          const terang = px[i] * 0.299 + px[i + 1] * 0.587 + px[i + 2] * 0.114;
          if (terang >= 160) {
            data[y * byteBaris + (x >> 3)] |= 1 << (7 - (x & 7));
          }
        }
      }
      const header = new Uint8Array(22);
      const view = new DataView(header.buffer);
      header.set([0x58, 0x54, 0x47, 0x00], 0);
      view.setUint16(4, lebar, true);
      view.setUint16(6, tinggi, true);
      view.setUint32(10, data.length, true);
      const blob = new Uint8Array(22 + data.length);
      blob.set(header, 0);
      blob.set(data, 22);
      return blob;
    }

    function bangunXtc(halaman) {
      const jumlah = halaman.length;
      const awalIndeks = 56;
      const awalData = awalIndeks + jumlah * 16;
      let ukuran = awalData;
      for (const blob of halaman) {
        ukuran += blob.length;
      }
      const file = new Uint8Array(ukuran);
      const view = new DataView(file.buffer);
      file.set([0x58, 0x54, 0x43, 0x00], 0);
      file[4] = 1;
      view.setUint16(6, jumlah, true);
      view.setUint32(12, 1, true);
      view.setBigUint64(24, BigInt(awalIndeks), true);
      view.setBigUint64(32, BigInt(awalData), true);
      let kursor = awalData;
      halaman.forEach((blob, i) => {
        const entri = awalIndeks + i * 16;
        view.setBigUint64(entri, BigInt(kursor), true);
        view.setUint32(entri + 8, blob.length, true);
        view.setUint16(entri + 12, 480, true);
        view.setUint16(entri + 14, 800, true);
        file.set(blob, kursor);
        kursor += blob.length;
      });
      return file;
    }

    async function eksporXtc(tombol) {
      if (!suratData || tombol.disabled) {
        return;
      }
      const asal = indeks;
      const label = tombol.textContent;
      tombol.disabled = true;
      const berkas = [];
      let gagal = false;
      try {
        indeks = 0;
        while (true) {
          tampilkanHalaman();
          const list = daftarHalaman();
          tombol.textContent = "EXPORT " + (indeks + 1) + "/" + list.length;
          berkas.push(bitmapXtg(await gambarLayar()));
          if (indeks >= list.length - 1) {
            break;
          }
          indeks += 1;
        }
        const file = bangunXtc(berkas);
        const nama = namaBerkasXtc();
        const url = URL.createObjectURL(new Blob([file], { type: "application/octet-stream" }));
        const unduh = document.createElement("a");
        unduh.href = url;
        unduh.download = nama;
        unduh.click();
        URL.revokeObjectURL(url);
      } catch (error) {
        gagal = true;
        tombol.textContent = "EXPORT gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "EXPORT gagal") {
            tombol.textContent = label;
          }
        }, 2500);
      } finally {
        indeks = asal;
        tampilkanHalaman();
        tombol.disabled = false;
        if (!gagal) {
          tombol.textContent = label;
        }
      }
    }

    function namaScreenshot() {
      const latin = (suratData.namaLatin || "surat").replace(/[^\w.-]+/g, "-");
      const list = daftarHalaman();
      const item = list[indeks];
      const halaman = !item || item.jenis === "cover" ? "cover" : String(indeks);
      return latin + "-" + halaman + ".jpg";
    }

    async function simpanScreenshot(tombol) {
      if (!suratData || tombol.disabled) {
        return;
      }
      const label = tombol.textContent;
      tombol.disabled = true;
      tombol.textContent = "SCREENSHOT…";
      try {
        const render = await muatKanvas();
        const stage = document.getElementById("page").cloneNode(true);
        stage.removeAttribute("id");
        stage.querySelectorAll("[id]").forEach((el) => el.removeAttribute("id"));
        stage.style.position = "fixed";
        stage.style.left = "-2000px";
        stage.style.top = "0";
        stage.style.transform = "none";
        document.body.append(stage);
        let canvas;
        try {
          canvas = await render(stage, {
            width: 480,
            height: 800,
            scale: 1,
            backgroundColor: stage.querySelector(".cover") ? "#ffffff" : "#e4e2dc",
            logging: false,
          });
        } finally {
          stage.remove();
        }
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, "image/jpeg", 0.92));
        if (!blob) {
          throw new Error("JPG gagal dibuat.");
        }
        const url = URL.createObjectURL(blob);
        const unduh = document.createElement("a");
        unduh.href = url;
        unduh.download = namaScreenshot();
        unduh.click();
        URL.revokeObjectURL(url);
      } catch (error) {
        tombol.textContent = "SCREENSHOT gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "SCREENSHOT gagal") {
            tombol.textContent = label;
          }
        }, 2500);
        return;
      } finally {
        tombol.disabled = false;
        if (tombol.textContent === "SCREENSHOT…") {
          tombol.textContent = label;
        }
      }
    }

    function namaBerkasXtc() {
      const nomor = String(suratData.nomor).padStart(3, "0");
      const latin = (suratData.namaLatin || "surat").replace(/[^\w.-]+/g, "-");
      return nomor + "-" + latin + ".xtc";
    }

    async function kirimXtc(nama, file) {
      const ukuran = 1500000;
      for (let i = 0; i < file.length; i += ukuran) {
        const res = await fetch("simpan-xtc.php?nama=" + encodeURIComponent(nama) + "&tambah=" + (i ? "1" : "0"), {
          method: "POST",
          headers: { "Content-Type": "application/octet-stream" },
          body: file.subarray(i, i + ukuran),
        });
        if (!res.ok) {
          throw new Error(await res.text());
        }
      }
    }

    async function simpanXtcSekarang() {
      const asal = indeks;
      const berkas = [];
      indeks = 0;
      try {
        while (true) {
          tampilkanHalaman();
          const list = daftarHalaman();
          berkas.push(bitmapXtg(await gambarLayar()));
          if (indeks >= list.length - 1) {
            break;
          }
          indeks += 1;
        }
        const file = bangunXtc(berkas);
        const nama = namaBerkasXtc();
        await kirimXtc(nama, file);
        return { nama, halaman: berkas.length, byte: file.length };
      } finally {
        indeks = asal;
        tampilkanHalaman();
      }
    }

    function memuatAyat(item, nomor) {
      if (!item || item.jenis === "cover" || item.jenis === "penutup") {
        return false;
      }
      if (item.jenis === "grup") {
        return item.bagian.some((bagian) => bagian.ayat.nomorAyat === nomor);
      }
      return Boolean(item.ayat) && item.ayat.nomorAyat === nomor;
    }

    function lompatKeAyat(nilai) {
      if (!suratData) {
        return;
      }
      const tujuan = Number(nilai);
      if (!tujuan || tujuan < 1 || tujuan > Number(suratData.jumlahAyat)) {
        return;
      }
      const list = daftarHalaman();
      const indeksBaru = list.findIndex((item) => memuatAyat(item, tujuan));
      if (indeksBaru < 0) {
        return;
      }
      indeks = indeksBaru;
      tampilkanHalaman();
    }

    function pindah(langkah) {
      const list = daftarHalaman();
      const berikut = indeks + langkah;
      if (!suratData || berikut < 0 || berikut > list.length - 1) {
        return;
      }
      indeks = berikut;
      tampilkanHalaman();
    }

    lompatForm.addEventListener("submit", (event) => {
      event.preventDefault();
      lompatKeAyat(lompatEl.value);
    });
    screenshotBtn.addEventListener("click", () => simpanScreenshot(screenshotBtn));
    beforeBtn.addEventListener("click", () => pindah(-1));
    afterBtn.addEventListener("click", () => pindah(1));
    cariEl.addEventListener("input", tampilkanDaftar);

    ambil("/surat")
      .then((data) => {
        surat = data;
        daftarStatus.hidden = true;
        cekValidasi();
        tampilkanDaftar();
        bukaSurat(1);
      })
      .catch((error) => {
        daftarStatus.className = "status error";
        daftarStatus.textContent = error.message || "Gagal memuat daftar surat.";
      });
  </script>
</body>
</html>
