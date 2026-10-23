<?php

declare(strict_types=1);

require_once __DIR__ . '/simpan-data.php';

$judul = 'Quran to Xteink';
$berkasSalinan = __DIR__ . '/data/quran.sqlite';
$waktuSalinan = is_file($berkasSalinan) ? filemtime($berkasSalinan) : false;
$teksSalinan = $waktuSalinan === false ? 'belum ada' : teksWaktuSalinan($waktuSalinan);
$isoSalinan = $waktuSalinan === false
    ? ''
    : (new DateTimeImmutable('@' . $waktuSalinan))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format(DateTimeInterface::ATOM);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>QTX</title>
  <style>
    /* Font lokal (folder font/), tanpa Google Fonts: bisa offline, sama dengan font yang ditanam di EPUB. */
    @font-face { font-family: "Lato"; src: url("font/Lato-Regular.ttf") format("truetype"); font-weight: 400; font-style: normal; font-display: block; }
    @font-face { font-family: "Lato"; src: url("font/Lato-Italic.ttf") format("truetype"); font-weight: 400; font-style: italic; font-display: block; }
    @font-face { font-family: "Lato"; src: url("font/Lato-Bold.ttf") format("truetype"); font-weight: 700; font-style: normal; font-display: block; }
    @font-face { font-family: "Noto Sans Arabic"; src: url("font/NotoSansArabic-Light.ttf") format("truetype"); font-weight: 300; font-style: normal; font-display: block; }
    @font-face { font-family: "Noto Sans Arabic"; src: url("font/NotoSansArabic-Regular.ttf") format("truetype"); font-weight: 400; font-style: normal; font-display: block; }
    @font-face { font-family: "Noto Sans Arabic"; src: url("font/NotoSansArabic-Bold.ttf") format("truetype"); font-weight: 700; font-style: normal; font-display: block; }
    @font-face { font-family: "Noto Kufi Arabic"; src: url("font/NotoKufiArabic-var.ttf") format("truetype"); font-weight: 100 900; font-style: normal; font-display: block; }
    @font-face { font-family: "Scheherazade New"; src: url("font/ScheherazadeNew-Regular.ttf") format("truetype"); font-weight: 400; font-style: normal; font-display: block; }

    :root {
      color-scheme: dark;
      --md-primary: #9bb4c9;
      --md-on-primary: #1a2228;
      --md-primary-container: #243038;
      --md-on-primary-container: #d5dbe2;
      --md-surface: #1c1e22;
      --md-surface-container: #24262b;
      --md-surface-container-high: #2c2f35;
      --md-on-surface: #c5c8ce;
      --md-on-surface-variant: #8d9199;
      --md-outline: #3a3e46;
      --md-outline-variant: #2e3238;
      --accent-glow: rgba(155, 180, 201, 0.22);
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
      --xtc-lebar: 55.418mm;
      --xtc-tinggi: 92.364mm;
      --xtc-zoom: 1.32;
      --a6-lebar: 107.2mm;
      --a6-tinggi: 144.8mm;
      --epub-zoom: 1;
      --canvas: #1c1e22;
      --panel: #1c1e22;
      --panel-2: #1c1e22;
      --ink: #f2f1ec;
      --paper: #100f0e;
      --paper-2: #1a1917;
      font-family: Lato, sans-serif;
      color: var(--md-on-surface);
      background: var(--canvas);
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      min-height: 100vh;
    }

    .shell {
      height: 100vh;
    }

    .app {
      display: grid;
      grid-template-columns: 390px minmax(0, 1fr);
      height: 100%;
      min-height: 0;
      background: var(--panel);
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 18px 16px 14px;
    }

    .brand-mark {
      width: 36px;
      height: 36px;
      flex: 0 0 36px;
      display: grid;
      place-items: center;
      border-radius: 10px;
      background: #24262b;
      border: 1px solid rgba(255, 255, 255, 0.05);
      color: #d5d8de;
    }

    .brand h1 {
      margin: 0;
      font-size: 0.95rem;
      font-weight: 600;
      letter-spacing: -0.01em;
    }

    .brand p {
      margin: 2px 0 0;
      color: var(--md-on-surface-variant);
      font-size: 0.7rem;
      line-height: 1.35;
    }

    .brand {
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .app-bar {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: var(--space-4);
      min-height: 68px;
      padding: 0 18px;
      background: transparent;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .app-actions {
      display: flex;
      align-items: center;
      gap: var(--space-2);
      margin-left: auto;
    }

    .bahasa-pilih {
      min-height: 42px;
      padding: 0 40px 0 16px;
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      background-color: #24262b;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M6 9l6 6 6-6' stroke='%23c5c8ce' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 16px 16px;
      color: var(--md-on-surface);
      font: inherit;
      font-size: 0.9375rem;
      font-weight: 500;
      cursor: pointer;
      appearance: none;
    }

    .bahasa-pilih:hover {
      background: var(--md-surface-container);
    }

    .revalidasi-btn {
      min-height: 42px;
      padding: 0 16px;
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      background: #24262b;
      color: var(--md-on-surface);
      font: inherit;
      font-size: 0.9375rem;
      font-weight: 500;
      letter-spacing: 0;
      cursor: pointer;
    }

    .revalidasi-btn:hover:not(:disabled) {
      background: var(--md-surface-container);
    }

    .revalidasi-btn:disabled {
      opacity: 0.7;
      cursor: default;
    }

    .revalidasi-dialog {
      width: min(420px, calc(100vw - 32px));
      margin: auto;
      padding: 24px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      background: #22252a;
      color: var(--md-on-surface);
      box-shadow: 0 24px 70px rgba(0, 0, 0, 0.45);
    }

    .revalidasi-dialog::backdrop {
      background: rgba(0, 0, 0, 0.64);
    }

    .revalidasi-dialog h2 {
      margin: 0 0 8px;
      font-size: 1.375rem;
      font-weight: 500;
    }

    .revalidasi-dialog p {
      margin: 0;
      color: var(--md-on-surface-variant);
      font-size: 0.875rem;
      line-height: 1.5;
    }

    .progress-track {
      margin-top: 20px;
      height: 4px;
      border-radius: 999px;
      background: var(--md-outline-variant);
      overflow: hidden;
    }

    .progress-bar {
      width: 0%;
      height: 100%;
      background: var(--md-primary);
    }

    .progress-meta {
      margin-top: 8px;
      font-size: 0.75rem;
    }

    .revalidasi-dialog .revalidasi-btn {
      margin-top: 16px;
    }

    .workspace {
      display: flex;
      flex-direction: column;
      min-width: 0;
      min-height: 0;
      background: var(--panel-2);
    }

    .layout {
      display: grid;
      grid-template-columns: minmax(280px, 1fr) auto;
      align-items: stretch;
      flex: 1;
      min-height: 0;
    }

    .pane-list {
      border-right: 1px solid rgba(255, 255, 255, 0.05);
      background: var(--panel);
      display: flex;
      flex-direction: column;
      min-height: 0;
      overflow: hidden;
    }

    .search-wrap {
      padding: 14px 16px 12px;
    }

    .validasi {
      margin: 0 16px 12px 26px;
      text-align: start;
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
      margin: 0 0 10px;
      padding-left: 10px;
      text-align: start;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #8d9199;
    }

    input[type="search"] {
      width: 100%;
      min-height: 44px;
      padding: 0 16px;
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 10px;
      background: #17191d;
      color: var(--md-on-surface);
      font: inherit;
      font-size: 1rem;
      text-align: start;
    }

    input:focus-visible,
    select:focus-visible,
    button:focus-visible {
      outline: 2px solid var(--md-primary);
      outline-offset: 2px;
    }

    .surat-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin: 0;
      padding: 8px 14px 16px;
      overflow: auto;
      flex: 1;
      min-height: 0;
    }

    .paginasi {
      flex-shrink: 0;
      display: grid;
      grid-template-columns: repeat(9, minmax(0, 1fr));
      gap: 6px;
      padding: 12px 14px 14px;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .paginasi[hidden] {
      display: none;
    }

    .paginasi button {
      min-height: 28px;
      padding: 0;
      border: 1px solid rgba(255, 255, 255, 0.06);
      border-radius: 8px;
      background: #24262b;
      color: #b0b4bb;
      font: inherit;
      font-size: 0.75rem;
      cursor: pointer;
    }

    .paginasi button:hover {
      background: #2c2f35;
      color: #d5d8de;
    }

    .paginasi button[aria-current="page"] {
      background: #2c333b;
      color: #d8dce2;
      border-color: rgba(155, 180, 201, 0.35);
    }

    .surat-list button {
      position: relative;
      width: 100%;
      display: grid;
      grid-template-columns: 40px minmax(0, 1fr);
      gap: 14px;
      align-items: center;
      min-height: 68px;
      padding: 12px 14px;
      border: 0;
      border-radius: 12px;
      background: transparent;
      color: #c4c7cd;
      text-align: start;
      font: inherit;
      cursor: pointer;
    }

    .surat-list button:hover {
      background: rgba(255, 255, 255, 0.03);
    }

    .surat-list button[aria-current="true"] {
      border-radius: calc(10px + 14px);
      background: linear-gradient(90deg, rgba(155, 180, 201, 0.14), rgba(155, 180, 201, 0.03) 72%);
      box-shadow: inset 0 0 0 1px rgba(155, 180, 201, 0.12);
      color: #d8dce2;
    }

    .surat-list button[aria-current="true"]::before {
      content: "";
      position: absolute;
      left: -10px;
      top: 10px;
      bottom: 10px;
      width: 3px;
      border-radius: 3px;
      background: var(--md-primary);
      box-shadow: none;
    }

    .nomor {
      width: 40px;
      height: 40px;
      display: grid;
      place-items: center;
      border-radius: 10px;
      background: #2a2d33;
      color: #b4b8bf;
      font-size: 0.875rem;
      font-weight: 600;
    }

    button[aria-current="true"] .nomor {
      background: rgba(155, 180, 201, 0.14);
      color: #d5e0ea;
      box-shadow: none;
    }

    .nama {
      font-size: 1.0625rem;
      font-weight: 500;
    }

    .meta {
      display: block;
      margin-top: 4px;
      color: var(--md-on-surface-variant);
      font-size: 0.9375rem;
    }

    button[aria-current="true"] .meta {
      color: #aeb3ba;
    }

    .pane-detail {
      padding: 28px 28px 32px;
      overflow: auto;
      min-height: 0;
      border-right: 1px solid rgba(255, 255, 255, 0.06);
      text-align: start;
    }

    .pane-page {
      display: flex;
      align-items: center;
      gap: var(--space-3);
      padding: var(--space-6);
      overflow: auto;
    }

    .layar-kolom {
      display: flex;
      flex-direction: column;
      align-items: stretch;
      gap: var(--space-3);
    }

    .ukur-layar {
      position: absolute;
      left: -10000px;
      top: 0;
      width: 480px;
      height: 800px;
      overflow: hidden;
      pointer-events: none;
    }

    .ukur-layar.is-epub {
      width: var(--a6-lebar);
      height: var(--a6-tinggi);
    }

    .screen {
      position: relative;
      width: calc(var(--xtc-lebar) * var(--xtc-zoom));
      height: calc(var(--xtc-tinggi) * var(--xtc-zoom));
      overflow: hidden;
    }

    .screen.is-epub {
      width: calc(var(--a6-lebar) * var(--epub-zoom));
      height: calc(var(--a6-tinggi) * var(--epub-zoom));
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.28);
    }

    .viewer-toggle {
      display: flex;
      flex: 0 0 auto;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      overflow: hidden;
      background: #24262b;
    }

    .viewer-toggle button {
      min-height: 42px;
      padding: 0 16px;
      border: 0;
      background: transparent;
      color: #b0b4bb;
      font: inherit;
      font-size: 0.9375rem;
      font-weight: 500;
      letter-spacing: 0;
      cursor: pointer;
    }

    body[data-mode="xtc"] .fitur-epub,
    body[data-mode="xtc"] .fitur-kindle,
    body[data-mode="epub"] .fitur-xteink,
    body[data-mode="epub"] .fitur-kindle,
    body[data-mode="kindle"] .fitur-xteink,
    body[data-mode="kindle"] .fitur-epub {
      display: none;
    }

    body[data-mode="xtc"] .fitur-halaman {
      display: none;
    }

    body[data-mode="kindle"] #halaman-global [data-halaman="halaman-1"],
    body[data-mode="kindle"] #halaman-global [data-halaman="penutup"] {
      display: none;
    }

    .screen.is-kindle {
      width: 520px;
      height: min(78vh, 900px);
      overflow: auto;
      background: #ffffff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.28);
    }

    .page.is-kindle {
      container-type: normal;
      position: relative;
      width: 100%;
      height: auto;
      min-height: 100%;
      transform: none;
      overflow: visible;
      color: #111111;
      background: #ffffff;
    }

    .page.is-kindle .page-body {
      height: auto;
      overflow: visible;
      padding: 28px 32px 48px;
    }

    .page.is-kindle .page-body.is-cover {
      padding: 0;
    }

    .page.is-kindle .lembar-gambar {
      display: block;
      width: 100%;
      height: auto;
    }

    .page.is-kindle .kindle-nama {
      margin: 0 0 8px;
      font-family: Bookerly, serif;
      font-size: 32px;
      font-weight: 700;
      line-height: 1.3;
      text-align: right;
    }

    .page.is-kindle .kindle-judul {
      margin: 0 0 4px;
      font-family: "Amazon Ember", Helvetica, sans-serif;
      font-size: 22px;
      font-weight: 700;
    }

    .page.is-kindle .kindle-meta,
    .page.is-kindle .kindle-latin,
    .page.is-kindle .kindle-makna,
    .page.is-kindle .kindle-daftar-item {
      font-family: Bookerly, Palatino, "Palatino Linotype", Georgia, serif;
      font-size: 16px;
      line-height: 1.45;
    }

    .page.is-kindle .kindle-meta {
      margin: 0 0 28px;
    }

    .page.is-kindle .kindle-arab {
      margin: 0 0 8px;
      font-family: Bookerly, serif;
      font-size: 28px;
      font-weight: 400;
      line-height: 1.9;
      text-align: right;
    }

    .page.is-kindle .kindle-latin {
      margin: 0;
      font-style: italic;
    }

    .page.is-kindle .kindle-makna {
      margin: 8px 0 0;
    }

    .page.is-kindle .kindle-ayat {
      margin: 0 0 22px;
      padding: 0 0 16px;
      border-bottom: 1px solid #d5d5d5;
      break-inside: avoid;
      page-break-inside: avoid;
    }

    .page.is-kindle .kindle-ayat:last-child {
      margin-bottom: 0;
      border-bottom: 0;
    }

    .page.is-kindle .kindle-daftar {
      margin: 18px 0 0;
      padding: 0;
      list-style: none;
    }

    .page.is-kindle .kindle-daftar li {
      margin: 0;
      padding: 10px 0;
      border-bottom: 1px solid #d5d5d5;
    }

    .page.is-kindle .kindle-daftar-item {
      display: block;
      width: 100%;
      margin: 0;
      padding: 0;
      border: 0;
      background: transparent;
      color: inherit;
      text-align: left;
      cursor: pointer;
    }

    body[data-mode="kindle"] .chevron,
    body[data-mode="kindle"] .screenshot-btn,
    body[data-mode="kindle"] .lompat {
      display: none;
    }

    .viewer-toggle button + button {
      border-left: 1px solid rgba(255, 255, 255, 0.08);
    }

    .viewer-toggle button:hover {
      background: rgba(255, 255, 255, 0.04);
    }

    .viewer-toggle button[aria-pressed="true"] {
      background: #2c333b;
      color: #d5dbe2;
      box-shadow: none;
    }

    .viewer-toggle button:disabled {
      opacity: 0.7;
      cursor: default;
    }

    .screenshot-btn {
      align-self: center;
      width: 40px;
      height: 40px;
      min-height: 40px;
      padding: 0;
      display: grid;
      place-items: center;
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      background: #24262b;
      color: var(--md-on-surface);
      cursor: pointer;
    }

    .screenshot-btn:hover:not(:disabled) {
      background: #2c2f35;
      color: #d5d8de;
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
      font-size: 1rem;
      font-weight: 500;
      color: var(--md-on-surface-variant);
    }

    .lompat input {
      width: 100%;
      min-height: 44px;
      padding: 0 16px;
      font-size: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      background: #24262b;
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
      transform: scale(calc(var(--xtc-lebar) * var(--xtc-zoom) / 480px));
      transform-origin: top left;
      color: #1a1a1a;
      background-color: #e4e2dc;
      background-image: radial-gradient(rgba(0, 0, 0, 0.045) 0.55px, transparent 0.55px);
      background-size: 3px 3px;
      container-type: size;
    }

    .page.is-epub {
      width: var(--a6-lebar);
      height: var(--a6-tinggi);
      transform: scale(var(--epub-zoom));
      color: #111111;
      background-color: #ffffff;
      background-image: none;
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
      font-family: Lato, system-ui, sans-serif;
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
      width: 40px;
      height: 40px;
      flex: 0 0 40px;
      display: grid;
      place-items: center;
      padding: 0;
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      background: #24262b;
      color: var(--md-on-surface);
      cursor: pointer;
    }

    .chevron:hover:not(:disabled) {
      background: #2c2f35;
      color: #d5d8de;
    }

    .chevron:disabled {
      color: var(--md-outline-variant);
      cursor: default;
    }

    .detail-head {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: var(--space-2);
      text-align: start;
    }

    .deskripsi {
      margin: var(--space-6) 0 0;
      max-width: 62ch;
      line-height: 1.6;
    }

    .export-row {
      display: flex;
      flex-wrap: wrap;
      gap: var(--space-2);
      margin-top: var(--space-4);
    }

    .export-btn {
      align-self: flex-start;
      min-height: 44px;
      padding: 0 18px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      background: #2a2d33;
      color: #d5d8de;
      font: inherit;
      font-size: 0.9375rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      cursor: pointer;
    }

    .export-btn.secondary {
      background: transparent;
      color: #b7bbc2;
      box-shadow: none;
    }

    .export-btn.secondary:hover:not(:disabled) {
      background: var(--md-surface-container);
    }

    .export-btn:disabled {
      opacity: 0.7;
      cursor: default;
    }

    .halaman-tambahan {
      display: flex;
      flex-direction: column;
      gap: var(--space-1);
      margin: 12px 14px 8px;
      padding-top: 14px;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
      flex-shrink: 0;
    }

    .surat-list::-webkit-scrollbar,
    .pane-detail::-webkit-scrollbar {
      width: 10px;
    }

    .surat-list::-webkit-scrollbar-thumb,
    .pane-detail::-webkit-scrollbar-thumb {
      background: #2c2c2c;
      border: 2px solid transparent;
      border-radius: 999px;
      background-clip: padding-box;
    }

    .halaman-tambahan h3 {
      margin: 0 8px 6px;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #8d9199;
    }

    .halaman-tambahan-list {
      display: flex;
      flex-direction: column;
    }

    .halaman-tambahan-list button {
      position: relative;
      min-height: 48px;
      padding: 12px 16px;
      border: 0;
      border-radius: 12px;
      background: transparent;
      color: #c4c7cd;
      text-align: start;
      font: inherit;
      font-size: 1.0625rem;
      cursor: pointer;
    }

    .halaman-tambahan-list button:hover {
      background: rgba(255, 255, 255, 0.04);
    }

    .halaman-tambahan-list button[aria-current="true"] {
      background: linear-gradient(90deg, rgba(155, 180, 201, 0.14), rgba(155, 180, 201, 0.03) 72%);
      box-shadow: inset 0 0 0 1px rgba(155, 180, 201, 0.12);
      color: #d8dce2;
    }

    .halaman-tambahan .export-btn {
      margin-top: var(--space-2);
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
      font-weight: 600;
      letter-spacing: -0.02em;
      text-align: start;
    }

    .judul-arab {
      font-family: "Noto Kufi Arabic", sans-serif;
      font-weight: 500;
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
      border-top: 1px solid #424750;
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

    .teks-id[lang="ja"],
    .cover-sumber[lang="ja"],
    .cover-nama[lang="ja"],
    .cover-arti[lang="ja"],
    .cover-meta[lang="ja"],
    .nama-surah[lang="ja"],
    .ayat-no[lang="ja"],
    .deskripsi[lang="ja"] {
      font-family: "Hiragino Sans", "Hiragino Kaku Gothic ProN", "Yu Gothic", "Noto Sans JP", sans-serif;
    }

    .page.is-epub .page-body {
      padding: 6mm 10mm 14mm;
    }

    .page.is-epub .page-body.is-cover {
      padding: 0;
    }

    .page.is-epub .nomor-halaman {
      bottom: 6mm;
      font-size: 9pt;
    }

    .page.is-epub .nama-surah {
      margin-bottom: 3mm;
      font-size: 9pt;
    }

    .page.is-epub .ayat {
      padding: 2mm 0;
      border-top-color: #cfcfcf;
    }

    .page.is-epub .ayat-top {
      margin-bottom: 3mm;
    }

    .page.is-epub .ayat-no {
      font-size: 9pt;
      color: #444444;
    }

    .page.is-epub .teks-arab {
      margin-bottom: 3mm;
      font-size: 16pt;
      line-height: 1.9;
    }

    .page.is-epub .ayat-no-arab {
      font-size: 70%;
    }

    .page.is-epub .teks-latin,
    .page.is-epub .teks-id {
      font-size: 9pt;
      line-height: 1.4;
    }

    .page.is-epub .teks-id {
      margin-top: 2.5mm;
    }

    .page.is-epub .penutup-arab {
      font-size: 18pt;
    }

    .page.is-epub .cover.is-tengah {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: flex-end;
      padding: 0 10.8mm;
      gap: 2mm;
    }

    .page.is-epub .cover.is-tengah p {
      position: static;
      right: auto;
    }

    .page.is-epub .cover-arab {
      font-size: 10.8mm;
      line-height: 1.1;
    }

    .page.is-epub .cover-nama {
      font-size: 16pt;
      line-height: 1.3;
    }

    .page.is-epub .cover-arti {
      font-size: 13pt;
      line-height: 1.3;
    }

    .page.is-epub .cover-meta {
      font-size: 10pt;
      line-height: 1.4;
    }

    .page.is-epub .cover-sumber {
      top: auto;
      right: 10.8mm;
      bottom: 8mm;
      left: 12mm;
      font-size: 7.2pt;
    }

    .page.is-epub .daftar-isi {
      color: #111111;
      font-family: Lato, sans-serif;
    }

    .page.is-epub .daftar-isi h2 {
      margin: 0 0 4mm;
      font-size: 13pt;
      font-weight: 700;
      text-align: left;
    }

    .page.is-epub .daftar-isi-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      column-gap: 4mm;
      row-gap: 3mm;
    }

    .page.is-epub .isi-item,
    .page.is-epub button.isi-item,
    .page.is-epub a.isi-item {
      display: grid;
      grid-template-columns: 9mm minmax(0, 1fr);
      gap: 2mm;
      align-items: center;
      width: 100%;
      min-width: 0;
      margin: 0;
      padding: 0;
      border: 0;
      background: transparent;
      color: inherit;
      font: inherit;
      text-align: start;
      text-decoration: none;
      cursor: pointer;
    }

    .page.is-epub .isi-nomor {
      width: 9mm;
      height: 9mm;
      display: grid;
      place-items: center;
      border-radius: 1.6mm;
      background: #e4e4e4;
      color: #111111;
      font-size: 8pt;
      font-weight: 500;
    }

    .page.is-epub .isi-nama,
    .page.is-epub .isi-meta {
      display: block;
      color: #111111;
      line-height: 1.25;
    }

    .page.is-epub .isi-nama {
      font-size: 9pt;
      font-weight: 700;
    }

    .page.is-epub .isi-meta {
      margin-top: 0.4mm;
      font-size: 7.5pt;
      font-weight: 400;
    }

    .page.is-epub .lembar-gambar {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    body[data-mode="xtc"] .screen {
      background: var(--ink);
    }

    body[data-mode="xtc"] .page:not(.is-export) {
      background-color: var(--ink);
      color: var(--paper-2);
    }

    body[data-mode="xtc"] .page:not(.is-export) * {
      color: var(--paper-2);
    }

    body[data-mode="xtc"] .page:not(.is-export) .page-body.is-cover,
    body[data-mode="xtc"] .page:not(.is-export) .cover {
      background: var(--ink);
      color: var(--paper-2);
    }

    body[data-mode="xtc"] .page:not(.is-export) .ayat {
      border-top-color: var(--paper-2);
    }

  </style>
</head>
<body data-mode="xtc">
  <div class="shell">
  <div class="app">
    <aside class="pane-list">
      <div class="brand">
        <span class="brand-mark" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <rect x="4" y="3" width="12" height="18" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M16 7h3a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-3" stroke="currentColor" stroke-width="1.8"/>
          </svg>
        </span>
        <div>
          <h1><?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') ?></h1>
          <p><span id="salinan-teks">Salinan lokal dari equran.id API v2</span> · <time id="diperbarui" datetime="<?= htmlspecialchars($isoSalinan, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($teksSalinan, ENT_QUOTES, 'UTF-8') ?></time></p>
        </div>
      </div>
      <div class="search-wrap">
        <label for="cari">Surat</label>
        <input id="cari" type="search" placeholder="Nama atau arti" autocomplete="off">
      </div>
      <p id="validasi" class="validasi" hidden></p>
      <p id="daftar-status" class="status" style="padding: 0 16px;">Memuat daftar surat…</p>
      <section id="halaman-global" class="halaman-tambahan fitur-halaman" aria-label="Halaman">
        <h3>HALAMAN</h3>
        <div class="halaman-tambahan-list">
          <button type="button" data-halaman="cover">Cover</button>
          <button type="button" data-halaman="halaman-1">Halaman 1</button>
          <button type="button" data-halaman="daftar-isi">Daftar isi</button>
          <button type="button" data-halaman="penutup">Halaman penutup</button>
        </div>
        <button id="ekspor-quran" class="export-btn fitur-epub" type="button">EXPORT EPUB QUR'AN</button>
        <button id="ekspor-quran-kindle" class="export-btn fitur-kindle" type="button">EXPORT KINDLE QUR'AN</button>
      </section>
      <ul id="daftar" class="surat-list"></ul>
      <nav id="paginasi" class="paginasi" aria-label="Halaman surat" hidden></nav>
    </aside>
    <div class="workspace">
      <header class="app-bar">
        <div class="app-actions">
          <select id="bahasa" class="bahasa-pilih" aria-label="Terjemahan">
            <option value="id">Indonesia</option>
            <option value="en">English</option>
            <option value="ja">日本語</option>
          </select>
          <button id="revalidasi" class="revalidasi-btn" type="button">Revalidate</button>
          <div class="viewer-toggle" role="group" aria-label="Format viewer">
            <button id="mode-xtc" type="button" aria-pressed="true">xteink</button>
            <button id="mode-epub" type="button" aria-pressed="false">EPUB</button>
            <button id="mode-kindle" type="button" aria-pressed="false">Kindle</button>
          </div>
        </div>
      </header>
      <div class="layout">
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
        <div id="screen" class="screen">
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
        <button id="screenshot" class="screenshot-btn" type="button" disabled aria-label="Screenshot" title="Screenshot">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 4H5a1 1 0 0 0-1 1v2M17 4h2a1 1 0 0 1 1 1v2M7 20H5a1 1 0 0 1-1-1v-2M17 20h2a1 1 0 0 0 1-1v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/>
          </svg>
        </button>
      </div>
      <button id="after" class="chevron" type="button" aria-label="After" disabled>
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
          <path fill="currentColor" d="M8.6 16.6 10 18l6-6-6-6-1.4 1.4L13.2 12z"/>
        </svg>
      </button>
    </section>
      </div>
    </div>
  </div>
  </div>
  <dialog id="revalidasi-dialog" class="revalidasi-dialog">
    <h2>Revalidate</h2>
    <p>Download the Quran text from the original API. Corrected descriptions and added translations stay unchanged.</p>
    <div id="revalidasi-track" class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-valuetext="0 / 114">
      <div id="revalidasi-bar" class="progress-bar"></div>
    </div>
    <p id="revalidasi-status" class="progress-meta">0 / 114</p>
    <button id="revalidasi-tutup" class="revalidasi-btn" type="button" hidden>Close</button>
  </dialog>
  <script type="module" src="http://127.0.0.1:5173/@vite/client"></script>
  <script>
    const SUMBER_INDONESIA = "Sumber data Al-Qur'an: EQuran.id\nTerjemahan Bahasa Indonesia:\nKementerian Agama Republik Indonesia";
    let bahasa = "id";
    let sumberLain = {};

    const daftarEl = document.getElementById("daftar");
    const daftarStatus = document.getElementById("daftar-status");
    const detailEl = document.getElementById("detail");
    const screenEl = document.getElementById("screen");
    const pageEl = document.getElementById("page");
    const pageBody = document.getElementById("page-body");
    const ukurLayar = document.createElement("div");
    ukurLayar.className = "ukur-layar";
    ukurLayar.setAttribute("aria-hidden", "true");
    const ukurPage = document.createElement("div");
    ukurPage.className = "page";
    const ukurBody = document.createElement("div");
    ukurBody.className = "page-body";
    ukurPage.append(ukurBody);
    ukurLayar.append(ukurPage);
    document.body.append(ukurLayar);
    let kanvas = pageBody;
    const beforeBtn = document.getElementById("before");
    const afterBtn = document.getElementById("after");
    const screenshotBtn = document.getElementById("screenshot");
    const lompatForm = document.getElementById("lompat-form");
    const lompatEl = document.getElementById("lompat");
    const cariEl = document.getElementById("cari");
    const validasiEl = document.getElementById("validasi");
    const modeXtcBtn = document.getElementById("mode-xtc");
    const modeEpubBtn = document.getElementById("mode-epub");
    const modeKindleBtn = document.getElementById("mode-kindle");
    const revalidasiBtn = document.getElementById("revalidasi");
    const revalidasiDialog = document.getElementById("revalidasi-dialog");
    const revalidasiTrack = document.getElementById("revalidasi-track");
    const revalidasiBar = document.getElementById("revalidasi-bar");
    const revalidasiStatus = document.getElementById("revalidasi-status");
    const revalidasiTutup = document.getElementById("revalidasi-tutup");
    const diperbaruiEl = document.getElementById("diperbarui");
    const paginasiEl = document.getElementById("paginasi");
    const UKURAN_SURAT = 7;
    let halamanDaftar = 1;
    let kunciScrollDaftar = false;
    let revalidasiBerjalan = false;
    const JUMLAH_AYAT_WAJIB = 6236;
    const suratGagal = new Map();

    let surat = [];
    let aktif = null;
    let ayatList = [];
    let suratData = null;
    let indeks = 0;
    let pecahan = new Map();
    let halamanCache = null;
    let modeViewer = "xtc";
    let halamanGlobal = null;
    let lembarGlobal = 0;
    let kunciGlobal = false;
    let pecahanDaftar = null;
    // Hasil pengukuran layout per "mode:nomorSurat", supaya pindah mode/surat tidak mengukur ulang.
    const tataCache = new Map();
    let kunciTata = null;

    function simpanTata() {
      if (kunciTata) {
        tataCache.set(kunciTata, { pecahan, halamanCache });
      }
    }

    function pakaiTata() {
      kunciTata = suratData ? modeViewer + ":" + bahasa + ":" + suratData.nomor : null;
      const tata = kunciTata ? tataCache.get(kunciTata) : null;
      pecahan = tata ? tata.pecahan : new Map();
      halamanCache = tata ? tata.halamanCache : null;
    }

    function tandaiAktif() {
      daftarEl.querySelectorAll("button[data-nomor]").forEach((button) => {
        button.setAttribute("aria-current", String(aktif === Number(button.dataset.nomor)));
      });
    }

    function angka(nilai) {
      const teks = String(nilai);
      const pemisah = antarmukaInggris() ? "," : ".";
      return teks.replace(/\B(?=(\d{3})+(?!\d))/g, pemisah);
    }

    function antarmukaInggris() {
      return bahasa === "en" || bahasa === "ja";
    }

    function tanpaTransliterasi() {
      return bahasa === "ja" && !halamanTampil();
    }

    function namaAntarmuka(item) {
      if (bahasa === "en") {
        return item.namaInggris || item.namaLatin;
      }
      if (bahasa === "ja") {
        return item.namaJepang || item.namaLatin;
      }
      return item.namaLatin;
    }

    function tempatAntarmuka(tempat) {
      if (!antarmukaInggris()) {
        return tempat;
      }
      return { Mekah: "Mecca", Madinah: "Medina" }[tempat] || tempat;
    }

    function metaDaftar(item) {
      if (antarmukaInggris()) {
        return item.jumlahAyat + " verses · " + tempatAntarmuka(item.tempatTurun);
      }
      return item.arti + " · " + item.jumlahAyat + " ayat · " + item.tempatTurun;
    }

    function terapkanAntarmuka() {
      const en = antarmukaInggris();
      document.documentElement.lang = bahasa === "ja" ? "ja" : en ? "en" : "id";
      document.querySelector('label[for="cari"]').textContent = en ? "Surah" : "Surat";
      cariEl.placeholder = en ? "Name or meaning" : "Nama atau arti";
      document.getElementById("salinan-teks").textContent = en
        ? "Local copy from equran.id API v2"
        : "Salinan lokal dari equran.id API v2";
      document.querySelector('label[for="lompat"]').textContent = en ? "Verse" : "Ayat";
      lompatEl.placeholder = en ? "Number" : "Nomor";
      document.querySelector("#bahasa").setAttribute("aria-label", en ? "Translation" : "Terjemahan");
      document.getElementById("paginasi").setAttribute("aria-label", en ? "Surah pages" : "Halaman surat");
      document.getElementById("detail").setAttribute("aria-label", en ? "Surah detail" : "Detail surat");
      document.querySelector(".pane-page").setAttribute("aria-label", en ? "Verse page" : "Halaman ayat");
      if (daftarStatus.textContent.startsWith("Memuat") || daftarStatus.textContent.startsWith("Loading")) {
        daftarStatus.textContent = en ? "Loading surah list…" : "Memuat daftar surat…";
      }
      const halaman = {
        cover: "Cover",
        "halaman-1": en ? "Page 1" : "Halaman 1",
        "daftar-isi": en ? "Contents" : "Daftar isi",
        penutup: en ? "Closing page" : "Halaman penutup",
      };
      document.querySelectorAll("#halaman-global [data-halaman]").forEach((btn) => {
        btn.textContent = halaman[btn.dataset.halaman] || btn.textContent;
      });
      const awalDetail = document.querySelector("#detail > p.status");
      if (awalDetail && !suratData) {
        awalDetail.textContent = en ? "Choose a surah to see details." : "Pilih surat untuk melihat detail.";
      }
      const awalHalaman = document.querySelector("#page-body > p.status");
      if (awalHalaman && !suratData) {
        awalHalaman.textContent = en ? "Choose a surah to load verses." : "Pilih surat untuk memuat ayat.";
      }
      if (surat.length) {
        cekValidasi();
        tampilkanDaftar();
      }
    }

    function cekValidasi() {
      const total = surat.reduce((jumlah, item) => jumlah + Number(item.jumlahAyat || 0), 0);
      const nomor = surat.map((item) => Number(item.nomor)).sort((a, b) => a - b);
      const urut = nomor.length === 114 && nomor.every((n, i) => n === i + 1);
      const sah = urut && total === JUMLAH_AYAT_WAJIB && suratGagal.size === 0;
      validasiEl.hidden = false;
      validasiEl.className = sah ? "validasi sah" : "validasi gagal";
      if (sah) {
        validasiEl.textContent = antarmukaInggris()
          ? "Valid · 114 surahs · " + angka(total) + " verses"
          : "Valid · 114 surat · " + angka(total) + " ayat";
        return;
      }
      const alasan = [];
      if (!urut) {
        alasan.push(antarmukaInggris()
          ? surat.length + " surahs, must be 114"
          : surat.length + " surat, harus 114");
      }
      if (total !== JUMLAH_AYAT_WAJIB) {
        alasan.push(antarmukaInggris()
          ? angka(total) + " verses, must be " + angka(JUMLAH_AYAT_WAJIB)
          : angka(total) + " ayat, harus " + angka(JUMLAH_AYAT_WAJIB));
      }
      for (const pesan of suratGagal.values()) {
        alasan.push(pesan);
      }
      validasiEl.textContent = (antarmukaInggris() ? "Invalid · " : "Tidak valid · ") + alasan.join(" · ");
    }

    function teks(value) {
      return String(value ?? "")
        .replace(/\u0091/g, "\u2018")
        .replace(/<[^>]*>/g, " ")
        .replace(/\s+/g, " ")
        .trim();
    }

    function namaBahasa(data) {
      if (bahasa === "en") {
        return data.namaInggris || data.namaLatin;
      }
      if (bahasa === "ja") {
        return data.namaJepang || data.namaLatin;
      }
      return data.namaLatin;
    }

    function artiBahasa(data) {
      if (bahasa === "en") {
        return data.namaInggris || data.arti;
      }
      if (bahasa === "ja") {
        return data.namaJepang || data.arti;
      }
      return data.arti;
    }

    function tempatBahasa(tempat) {
      const peta = {
        en: { Mekah: "Mecca", Madinah: "Medina" },
        ja: { Mekah: "マッカ", Madinah: "マディーナ" },
      };
      return (peta[bahasa] && peta[bahasa][tempat]) || tempat;
    }

    function halamanTampil() {
      return modeViewer === "epub";
    }

    function namaPratinjau(data) {
      return halamanTampil() ? data.namaLatin : namaBahasa(data);
    }

    function teksLatinBahasa(ayat) {
      if (bahasa === "en" && !halamanTampil() && ayat.teksLatinInggris) {
        return ayat.teksLatinInggris;
      }
      return ayat.teksLatin;
    }

    function teksTerjemahan(ayat) {
      if (bahasa === "en") {
        return ayat.teksInggris;
      }
      if (bahasa === "ja") {
        return ayat.teksJepang;
      }
      return ayat.teksIndonesia;
    }

    function teksSumberCover() {
      if (bahasa === "en") {
        return sumberLain.inggris || "";
      }
      if (bahasa === "ja") {
        return sumberLain.jepang || "";
      }
      return SUMBER_INDONESIA;
    }

    async function ambil(path) {
      const response = await fetch("data.php?ramping=1&bahasa=" + encodeURIComponent(bahasa) + "&r=" + encodeURIComponent(path));
      if (!response.ok) {
        throw new Error("API menjawab " + response.status);
      }
      const body = await response.json();
      if (body.code !== 200) {
        throw new Error(body.message || "Permintaan gagal");
      }
      return body.data;
    }

    function hasilCari() {
      const kata = cariEl.value.trim().toLowerCase();
      return surat.filter((item) => {
        const gabungan = (item.namaLatin + " " + (item.namaInggris || "") + " " + (item.namaJepang || "") + " " + item.arti + " " + item.nomor).toLowerCase();
        return gabungan.includes(kata);
      });
    }

    function tampilkanDaftar(opsi = {}) {
      const hasil = hasilCari();
      const totalHalaman = Math.max(1, Math.ceil(hasil.length / UKURAN_SURAT));
      if (halamanDaftar > totalHalaman) {
        halamanDaftar = totalHalaman;
      }
      if (halamanDaftar < 1) {
        halamanDaftar = 1;
      }
      const scroll = opsi.keAtas ? 0 : daftarEl.scrollTop;
      daftarEl.replaceChildren();
      gambarPaginasi(hasil.length);
      if (!hasil.length) {
        daftarStatus.hidden = false;
        daftarStatus.className = "status";
        daftarStatus.textContent = antarmukaInggris() ? "No matching surah." : "Tidak ada surat yang cocok.";
        return;
      }
      daftarStatus.hidden = true;
      hasil.forEach((item, indeks) => {
        const li = document.createElement("li");
        li.dataset.indeks = String(indeks);
        const button = document.createElement("button");
        button.type = "button";
        button.dataset.nomor = String(item.nomor);
        button.setAttribute("aria-current", String(aktif === item.nomor));
        const nomor = document.createElement("span");
        nomor.className = "nomor";
        nomor.textContent = String(item.nomor);
        const info = document.createElement("span");
        const nama = document.createElement("span");
        nama.className = "nama";
        nama.textContent = namaAntarmuka(item);
        const meta = document.createElement("span");
        meta.className = "meta";
        meta.textContent = metaDaftar(item);
        info.append(nama, meta);
        button.append(nomor, info);
        button.addEventListener("click", () => bukaSurat(item.nomor));
        li.append(button);
        daftarEl.append(li);
      });
      daftarEl.scrollTop = scroll;
    }

    let urutanMuat = 0;

    async function bukaSurat(nomor) {
      const tiket = ++urutanMuat;
      simpanTata();
      halamanGlobal = null;
      tandaiHalamanGlobal();
      aktif = nomor;
      tandaiAktif();
      try {
        const data = await ambil("/surat/" + nomor);
        if (tiket !== urutanMuat) {
          return;
        }
        gambarDetail(data);
      } catch (error) {
        if (tiket !== urutanMuat) {
          return;
        }
        const pesan = error.message || "Gagal memuat surat.";
        detailEl.replaceChildren();
        const gagal = document.createElement("p");
        gagal.className = "status error";
        gagal.textContent = pesan;
        detailEl.append(gagal);
        pageBody.classList.remove("is-cover", "is-penutup");
        pageBody.replaceChildren();
        const status = document.createElement("p");
        status.className = "status error";
        status.textContent = pesan;
        pageBody.append(status);
      }
    }

    function gambarDetail(data) {
      detailEl.replaceChildren();
      const head = document.createElement("div");
      head.className = "detail-head";
      const judul = document.createElement("div");
      const h2 = document.createElement("h2");
      h2.append(data.nomor + ". " + namaAntarmuka(data) + " - ");
      const arab = document.createElement("span");
      arab.className = "judul-arab";
      arab.lang = "ar";
      arab.textContent = data.nama;
      h2.append(arab);
      const ringkas = document.createElement("p");
      ringkas.className = "meta";
      ringkas.textContent = antarmukaInggris()
        ? data.jumlahAyat + " verses · " + tempatAntarmuka(data.tempatTurun)
        : data.arti + " · " + data.jumlahAyat + " ayat · " + data.tempatTurun;
      const deskripsi = document.createElement("p");
      deskripsi.className = "deskripsi";
      deskripsi.lang = bahasa;
      deskripsi.textContent = teks(data.deskripsi);
      judul.append(h2, ringkas);
      const exportRow = document.createElement("div");
      exportRow.className = "export-row";
      const exportXtc = document.createElement("button");
      exportXtc.type = "button";
      exportXtc.className = "export-btn fitur-xteink";
      exportXtc.textContent = "EXPORT XTEINK";
      exportXtc.addEventListener("click", () => eksporXtc(exportXtc));
      const exportEpub = document.createElement("button");
      exportEpub.type = "button";
      exportEpub.className = "export-btn secondary fitur-epub";
      exportEpub.textContent = "EXPORT EPUB";
      exportEpub.addEventListener("click", () => eksporEpub(exportEpub));
      const exportKindle = document.createElement("button");
      exportKindle.type = "button";
      exportKindle.className = "export-btn secondary fitur-kindle";
      exportKindle.textContent = "EXPORT KINDLE";
      exportKindle.addEventListener("click", () => eksporKindle(exportKindle));
      exportRow.append(exportXtc, exportEpub, exportKindle);
      head.append(judul, deskripsi, exportRow);
      detailEl.append(head);
      suratData = data;
      ayatList = data.ayat || [];
      if (ayatList.length !== Number(data.jumlahAyat)) {
        suratGagal.set(data.nomor, namaAntarmuka(data) + " " + ayatList.length + "/" + data.jumlahAyat + (antarmukaInggris() ? " verses" : " ayat"));
      } else {
        suratGagal.delete(data.nomor);
      }
      cekValidasi();
      pakaiTata();
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
          if (!tanpaTransliterasi()) {
            if (info.latin) {
              for (const cuplikan of info.latin) {
                list.push({ jenis: "latin", ayat, cuplikan });
              }
            } else {
              list.push({ jenis: "latin", ayat });
            }
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
        if ((info.susun === "tiga" || info.id) && !tanpaTransliterasi() && !info.latin) {
          gambarAyat(ayat, "latin");
          if (tidakMuat()) {
            const bagian = pecahBlok(ayat, "latin", teks(teksLatinBahasa(ayat)));
            if (bagian.length > 1) {
              info.latin = bagian;
            }
          }
        }
        if ((info.susun === "tiga" || info.id) && !info.id) {
          gambarAyat(ayat, "id");
          if (tidakMuat()) {
            const bagian = pecahTeksId(ayat);
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
        // Gambar sekali, lalu tambah satu ayat per langkah (bukan menggambar ulang seluruh grup).
        gambarBagian(bagian);
        while (i + bagian.length < mentah.length && mentah[i + bagian.length].jenis === "penuh") {
          const berikut = mentah[i + bagian.length];
          const el = buatBagian(berikut.ayat, berikut.jenis, berikut.cuplikan);
          kanvas.append(el);
          if (tidakMuat()) {
            el.remove();
            break;
          }
          bagian.push(berikut);
        }
        list.push(bagian.length === 1 ? item : { jenis: "grup", bagian });
        i += bagian.length;
      }
      return list;
    }

    function daftarHalaman() {
      if (!halamanCache) {
        const asal = kanvas;
        kanvas = ukurBody;
        ukurLayar.classList.toggle("is-epub", halamanTampil());
        ukurPage.classList.toggle("is-epub", halamanTampil());
        ukurBody.className = "page-body";
        try {
          rapikanSemua();
          const halaman = kemasHalaman(daftarMentah());
          halamanCache = halamanTampil() ? halaman : halaman.concat([{ jenis: "penutup" }]);
        } finally {
          kanvas = asal;
          ukurBody.replaceChildren();
        }
      }
      return halamanCache;
    }

    function tidakMuat() {
      return kanvas.scrollHeight > kanvas.clientHeight + 1;
    }

    function aturChevron(list) {
      beforeBtn.disabled = indeks <= 0;
      afterBtn.disabled = !suratData || !list || indeks >= list.length - 1;
    }

    function tampilkanHalaman() {
      if (modeViewer === "kindle") {
        tampilkanKindle();
        return;
      }
      if (halamanGlobal && halamanTampil() && !kunciGlobal) {
        tampilkanHalamanGlobal();
        return;
      }
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
        const bagian = pecahTeksId(item.ayat);
        if (bagian.length > 1) {
          info.id = bagian;
          halamanCache = null;
          tampilkanHalaman();
          return;
        }
      }
      if (item.jenis === "latin") {
        const bagian = pecahBlok(item.ayat, "latin", teks(teksLatinBahasa(item.ayat)));
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

    function pecahTeksId(ayat) {
      const sumber = teks(teksTerjemahan(ayat));
      if (bahasa === "ja" && !halamanTampil()) {
        return pecahTerjemahanJepang(ayat, sumber);
      }
      return pecahBlok(ayat, "id", sumber);
    }

    // Bahasa Jepang tidak dipisah spasi, jadi pecahBlok (per kata) tidak bisa memotongnya.
    function pecahTerjemahanJepang(ayat, sumber) {
      const utuh = sumber.trim();
      const hasil = [];
      let awal = 0;
      let lanjutan = false;
      let pengaman = 0;
      const bolehPutus = /[、。！？）」』〕］】…\s]/;
      while (awal < utuh.length && pengaman < 80) {
        pengaman += 1;
        const sisa = utuh.length - awal;
        let low = 1;
        let high = sisa;
        let terbaik = 0;
        while (low <= high) {
          const mid = Math.floor((low + high) / 2);
          gambarAyat(ayat, "id", susunCuplikanJepang(utuh.slice(awal, awal + mid), lanjutan, mid < sisa));
          if (!tidakMuat()) {
            terbaik = mid;
            low = mid + 1;
          } else {
            high = mid - 1;
          }
        }
        if (terbaik === 0) {
          terbaik = 1;
        }
        let potong = terbaik;
        if (awal + terbaik < utuh.length && terbaik > 1) {
          const jendela = utuh.slice(awal, awal + terbaik);
          const batas = Math.floor(jendela.length * 0.55);
          for (let i = jendela.length - 1; i >= batas; i -= 1) {
            if (bolehPutus.test(jendela[i])) {
              potong = i + 1;
              break;
            }
          }
        }
        const habis = awal + potong >= utuh.length;
        hasil.push(susunCuplikanJepang(utuh.slice(awal, awal + potong), lanjutan, !habis));
        if (habis) {
          break;
        }
        awal += potong;
        lanjutan = true;
      }
      return hasil;
    }

    function susunCuplikanJepang(isi, lanjutan, adaLagi) {
      let calon = isi.trim();
      if (lanjutan) {
        calon = "…" + calon;
      }
      if (adaLagi) {
        calon += "…";
      }
      return calon;
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
      nama.lang = bahasa;
      nama.textContent = suratData.nomor + ": " + namaPratinjau(suratData);
      const arti = document.createElement("p");
      arti.className = "cover-arti";
      arti.lang = bahasa;
      const makna = halamanTampil() ? suratData.arti : artiBahasa(suratData);
      arti.textContent = makna === namaPratinjau(suratData) ? "" : makna;
      const meta = document.createElement("p");
      meta.className = "cover-meta";
      meta.lang = bahasa;
      const satuan = halamanTampil() ? "ayat" : bahasa === "en" ? "verses" : bahasa === "ja" ? "節" : "ayat";
      const jumlah = suratData.jumlahAyat + (bahasa === "ja" && !halamanTampil() ? satuan : " " + satuan);
      meta.textContent = jumlah + " · " + (halamanTampil() ? suratData.tempatTurun : tempatBahasa(suratData.tempatTurun));
      const bagian = [arab, nama, arti, meta];
      if (halamanTampil()) {
        cover.classList.add("is-tengah");
      } else {
        const sumber = document.createElement("p");
        sumber.className = "cover-sumber";
        sumber.lang = bahasa;
        sumber.textContent = teksSumberCover();
        bagian.push(sumber);
      }
      cover.append(...bagian);
      pageBody.append(cover);
    }

    function tandaiHalamanGlobal() {
      const hidup = modeViewer === "epub" || modeViewer === "kindle";
      document.querySelectorAll("#halaman-global button").forEach((btn) => {
        btn.setAttribute("aria-current", String(hidup && btn.dataset.halaman === halamanGlobal));
      });
    }

    function urutanGlobal() {
      const daftar = bagiDaftar();
      const urutan = [{ id: "cover" }, { id: "halaman-1" }];
      daftar.forEach((isi, lembar) => urutan.push({ id: "daftar-isi", lembar, isi }));
      urutan.push({ id: "penutup" });
      return urutan;
    }

    function samakanLembarGlobal() {
      const urutan = urutanGlobal();
      const sekarang = urutan[lembarGlobal];
      if (!sekarang || sekarang.id !== halamanGlobal) {
        const ketemu = urutan.findIndex((item) => item.id === halamanGlobal);
        lembarGlobal = ketemu < 0 ? 0 : ketemu;
      }
      halamanGlobal = urutan[lembarGlobal].id;
      return urutan;
    }

    function aturChevronGlobal(urutan) {
      beforeBtn.disabled = lembarGlobal <= 0;
      afterBtn.disabled = lembarGlobal >= urutan.length - 1;
    }

    function tulisNomorGlobal(urutan) {
      const sekarang = urutan[lembarGlobal];
      if (!sekarang || sekarang.id === "cover" || sekarang.id === "halaman-1") {
        return;
      }
      const teks = urutan.filter((item) => item.id === "daftar-isi" || item.id === "penutup");
      const posisi = teks.indexOf(sekarang);
      if (posisi < 0) {
        return;
      }
      const nomor = document.createElement("p");
      nomor.className = "nomor-halaman";
      nomor.textContent = (posisi + 1) + " / " + teks.length;
      pageBody.append(nomor);
    }

    function gambarLembarGambar(src, alt) {
      pageBody.replaceChildren();
      pageBody.classList.add("is-cover");
      pageBody.classList.remove("is-penutup");
      const gambar = document.createElement("img");
      gambar.className = "lembar-gambar";
      gambar.src = src;
      gambar.alt = alt;
      pageBody.append(gambar);
    }

    function gambarCoverGlobal() {
      gambarLembarGambar("gambar/cover-epub.png?v=3", "Cover Al-Qur'an");
    }

    function gambarHalamanSatu() {
      gambarLembarGambar("gambar/halaman-1-epub.png?v=3", "Halaman 1");
    }

    function gambarDaftarPotongan(isi, denganJudul) {
      pageBody.replaceChildren();
      pageBody.classList.remove("is-cover");
      pageBody.classList.remove("is-penutup");
      const bungkus = document.createElement("div");
      bungkus.className = "daftar-isi";
      if (denganJudul) {
        const judul = document.createElement("h2");
        judul.textContent = antarmukaInggris() ? "Contents" : "Daftar isi";
        bungkus.append(judul);
      }
      const grid = document.createElement("div");
      grid.className = "daftar-isi-grid";
      for (const item of isi) {
        const baris = document.createElement(item.berkas ? "a" : "button");
        baris.className = "isi-item";
        if (item.berkas) {
          baris.href = item.berkas;
        } else {
          baris.type = "button";
          baris.addEventListener("click", () => bukaSurat(item.nomor));
        }
        const nomor = document.createElement("span");
        nomor.className = "isi-nomor";
        nomor.textContent = String(item.nomor);
        const info = document.createElement("span");
        const nama = document.createElement("span");
        nama.className = "isi-nama";
        nama.textContent = namaAntarmuka(item);
        const meta = document.createElement("span");
        meta.className = "isi-meta";
        const satuan = antarmukaInggris() ? " verses" : " ayat";
        const halaman = antarmukaInggris() ? "p. " : "hlm. ";
        meta.textContent = item.halamanAwal
          ? item.jumlahAyat + satuan + " · " + halaman + item.halamanAwal
          : item.jumlahAyat + satuan;
        info.append(nama, meta);
        baris.append(nomor, info);
        grid.append(baris);
      }
      bungkus.append(grid);
      pageBody.append(bungkus);
    }

    function susunDaftar(daftar) {
      const hasil = [];
      let mulai = 0;
      let pengaman = 0;
      while (mulai < daftar.length && pengaman < 40) {
        pengaman += 1;
        let terbaik = mulai - 1;
        let low = mulai;
        let high = daftar.length - 1;
        while (low <= high) {
          const mid = Math.floor((low + high) / 2);
          gambarDaftarPotongan(daftar.slice(mulai, mid + 1), hasil.length === 0);
          if (!tidakMuat()) {
            terbaik = mid;
            low = mid + 1;
          } else {
            high = mid - 1;
          }
        }
        if (terbaik < mulai) {
          terbaik = mulai;
        }
        hasil.push(daftar.slice(mulai, terbaik + 1));
        mulai = terbaik + 1;
      }
      return hasil.length ? hasil : [[]];
    }

    function bagiDaftar() {
      if (!pecahanDaftar) {
        pecahanDaftar = susunDaftar(surat);
      }
      return pecahanDaftar;
    }

    function tampilkanHalamanGlobal() {
      const urutan = samakanLembarGlobal();
      const sekarang = urutan[lembarGlobal];
      aturChevronGlobal(urutan);
      tandaiHalamanGlobal();
      if (sekarang.id === "cover") {
        gambarCoverGlobal();
        return;
      }
      if (sekarang.id === "halaman-1") {
        gambarHalamanSatu();
        return;
      }
      if (sekarang.id === "daftar-isi") {
        gambarDaftarPotongan(sekarang.isi, sekarang.lembar === 0);
      } else {
        gambarPenutup();
      }
      tulisNomorGlobal(urutan);
    }

    function bukaHalamanGlobal(id) {
      halamanGlobal = id;
      aktif = null;
      tandaiAktif();
      if (modeViewer === "kindle" && (id === "cover" || id === "daftar-isi")) {
        tampilkanKindle();
        return;
      }
      if (!halamanTampil()) {
        pecahanDaftar = null;
        aturModeViewer("epub");
      }
      const urutan = urutanGlobal();
      const ketemu = urutan.findIndex((item) => item.id === id);
      lembarGlobal = ketemu < 0 ? urutan.length - 1 : ketemu;
      tampilkanHalamanGlobal();
    }

    function angkaArab(nomor) {
      const digit = "٠١٢٣٤٥٦٧٨٩";
      return String(nomor).replace(/\d/g, (d) => digit[Number(d)]);
    }

    function nomorAkhirArab(nomor) {
      return " \u2066(" + angkaArab(nomor) + ")\u2069";
    }

    function potonganAwal(cuplikan) {
      return !cuplikan || !String(cuplikan).trimStart().startsWith("...");
    }

    function potonganAkhir(cuplikan) {
      return !cuplikan || !String(cuplikan).trimEnd().endsWith("...");
    }

    function buatBagian(ayat, jenis, cuplikan) {
      const section = document.createElement("article");
      section.className = "ayat";
      const epub = halamanTampil();
      if (!epub) {
        const top = document.createElement("div");
        top.className = "ayat-top";
        const no = document.createElement("span");
        no.className = "ayat-no";
        no.lang = bahasa;
        const satuanAyat = bahasa === "en" ? "Verse" : bahasa === "ja" ? "節" : "Ayat";
        no.textContent = satuanAyat + " " + ayat.nomorAyat + "/" + suratData.jumlahAyat;
        top.append(no);
        section.append(top);
      }
      if (jenis === "penuh" || jenis === "arab") {
        const arab = document.createElement("p");
        arab.className = "teks-arab";
        arab.lang = "ar";
        arab.dir = "rtl";
        arab.textContent = cuplikan || teks(ayat.teksArab);
        if (epub && potonganAkhir(cuplikan)) {
          const noArab = document.createElement("span");
          noArab.className = "ayat-no-arab";
          noArab.textContent = nomorAkhirArab(ayat.nomorAyat);
          arab.append(noArab);
        }
        section.append(arab);
      }
      if (!tanpaTransliterasi() && (jenis === "penuh" || jenis === "makna" || jenis === "latin")) {
        const latin = document.createElement("p");
        latin.className = "teks-latin";
        latin.textContent = (jenis === "latin" && cuplikan) || teks(teksLatinBahasa(ayat));
        section.append(latin);
      }
      if (jenis === "penuh" || jenis === "makna" || jenis === "id") {
        const indonesia = document.createElement("p");
        indonesia.className = "teks-id";
        indonesia.lang = bahasa;
        const sumberId = jenis === "id" ? cuplikan : null;
        let isiId = (jenis === "id" && cuplikan) || teks(teksTerjemahan(ayat));
        if (epub && potonganAwal(sumberId)) {
          isiId = "(" + ayat.nomorAyat + ") " + isiId;
        }
        indonesia.textContent = isiId;
        section.append(indonesia);
      }
      return section;
    }

    function gambarBagian(bagian) {
      kanvas.replaceChildren();
      kanvas.classList.remove("is-cover");
      kanvas.classList.remove("is-penutup");
      const judul = document.createElement("p");
      judul.className = "nama-surah";
      judul.lang = halamanTampil() ? "id" : bahasa;
      judul.textContent = namaPratinjau(suratData);
      kanvas.append(judul);
      for (const item of bagian) {
        kanvas.append(buatBagian(item.ayat, item.jenis, item.cuplikan));
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

    // html2canvas mengabaikan isolasi LTR dan membalik tanda kurung di teks RTL.
    function siapkanNomorArabKanvas(akar) {
      akar.querySelectorAll(".ayat-no-arab").forEach((el) => {
        el.dir = "ltr";
        el.style.direction = "ltr";
        el.style.unicodeBidi = "isolate";
        el.textContent = el.textContent.replace(/[\u2066\u2067\u2068\u2069\u202a\u202b\u202c]/g, "");
      });
    }

    // Salin #page ke luar layar lalu render dengan html2canvas.
    async function renderHalaman(opsi, ekspor = false) {
      const render = await muatKanvas();
      const stage = pageEl.cloneNode(true);
      if (ekspor) {
        stage.classList.add("is-export");
      }
      stage.removeAttribute("id");
      stage.querySelectorAll("[id]").forEach((el) => el.removeAttribute("id"));
      siapkanNomorArabKanvas(stage);
      stage.style.position = "fixed";
      stage.style.left = "-2000px";
      stage.style.top = "0";
      stage.style.transform = "none";
      document.body.append(stage);
      try {
        return await render(stage, { logging: false, ...opsi });
      } finally {
        stage.remove();
      }
    }

    function ukuranHalaman() {
      return { width: Math.round(pageEl.offsetWidth), height: Math.round(pageEl.offsetHeight) };
    }

    async function gambarLayar() {
      const canvas = await renderHalaman({ width: 480, height: 800, scale: 1, backgroundColor: "#ffffff" }, true);
      return canvas.getContext("2d", { willReadFrequently: true }).getImageData(0, 0, 480, 800);
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

    function nomorAyatTampil() {
      if (halamanGlobal || !halamanCache) {
        return null;
      }
      const item = halamanCache[indeks];
      if (!item || item.jenis === "cover" || item.jenis === "penutup" || item.jenis === "daftar-isi") {
        return null;
      }
      if (item.jenis === "grup") {
        return item.bagian[0].ayat.nomorAyat;
      }
      return item.ayat ? item.ayat.nomorAyat : null;
    }

    function gambarKindle() {
      pageBody.classList.remove("is-cover", "is-penutup");
      pageBody.replaceChildren();
      beforeBtn.disabled = true;
      afterBtn.disabled = true;
      if (!suratData) {
        const status = document.createElement("p");
        status.className = "status";
        status.textContent = antarmukaInggris() ? "Choose a surah to load verses." : "Pilih surat untuk memuat ayat.";
        pageBody.append(status);
        return;
      }
      const nama = document.createElement("p");
      nama.className = "kindle-nama";
      nama.lang = "ar";
      nama.dir = "rtl";
      nama.textContent = suratData.nama;
      const judul = document.createElement("h1");
      judul.className = "kindle-judul";
      judul.textContent = suratData.nomor + ". " + suratData.namaLatin;
      const meta = document.createElement("p");
      meta.className = "kindle-meta";
      meta.textContent = suratData.arti + " · " + suratData.jumlahAyat + " ayat · " + suratData.tempatTurun;
      pageBody.append(nama, judul, meta);
      for (const ayat of ayatList) {
        const bagian = document.createElement("section");
        bagian.className = "kindle-ayat";
        const arab = document.createElement("p");
        arab.className = "kindle-arab";
        arab.lang = "ar";
        arab.dir = "rtl";
        arab.append(document.createTextNode(teks(ayat.teksArab) + " "));
        const nomor = document.createElement("span");
        nomor.dir = "ltr";
        nomor.textContent = "(" + angkaArab(ayat.nomorAyat) + ")";
        arab.append(nomor);
        const latin = document.createElement("p");
        latin.className = "kindle-latin";
        latin.textContent = teks(teksLatinBahasa(ayat));
        const makna = document.createElement("p");
        makna.className = "kindle-makna";
        makna.textContent = "(" + ayat.nomorAyat + ") " + teks(teksTerjemahan(ayat));
        bagian.append(arab, latin, makna);
        pageBody.append(bagian);
      }
    }

    function gambarDaftarKindle() {
      pageBody.classList.remove("is-cover", "is-penutup");
      pageBody.replaceChildren();
      beforeBtn.disabled = true;
      afterBtn.disabled = true;
      const judul = document.createElement("h1");
      judul.className = "kindle-judul";
      judul.textContent = antarmukaInggris() ? "Contents" : "Daftar isi";
      const daftar = document.createElement("ol");
      daftar.className = "kindle-daftar";
      for (const item of surat) {
        const baris = document.createElement("li");
        const tombol = document.createElement("button");
        tombol.type = "button";
        tombol.className = "kindle-daftar-item";
        const satuan = antarmukaInggris() ? " verses" : " ayat";
        tombol.textContent = item.nomor + ". " + namaAntarmuka(item) + " — " + (item.arti || "") + " · " + item.jumlahAyat + satuan + " · " + (item.tempatTurun || "");
        tombol.addEventListener("click", () => bukaSurat(item.nomor));
        baris.append(tombol);
        daftar.append(baris);
      }
      pageBody.append(judul, daftar);
    }

    function tampilkanKindle() {
      tandaiHalamanGlobal();
      if (halamanGlobal === "cover") {
        gambarLembarGambar("gambar/halaman-1-epub.png?v=3", "Cover");
        beforeBtn.disabled = true;
        afterBtn.disabled = true;
        return;
      }
      if (halamanGlobal === "daftar-isi") {
        gambarDaftarKindle();
        return;
      }
      gambarKindle();
    }

    function aturModeViewer(mode, opsi = {}) {
      const berikutnya = mode === "epub" || mode === "kindle" ? mode : "xtc";
      const ganti = modeViewer !== berikutnya || opsi.paksa;
      const ayat = opsi.paksa ? null : nomorAyatTampil();
      simpanTata();
      modeViewer = berikutnya;
      const epub = modeViewer === "epub";
      const kindle = modeViewer === "kindle";
      document.body.dataset.mode = modeViewer;
      screenEl.classList.toggle("is-epub", epub);
      pageEl.classList.toggle("is-epub", epub);
      screenEl.classList.toggle("is-kindle", kindle);
      pageEl.classList.toggle("is-kindle", kindle);
      modeXtcBtn.setAttribute("aria-pressed", String(modeViewer === "xtc"));
      modeEpubBtn.setAttribute("aria-pressed", String(epub));
      modeKindleBtn.setAttribute("aria-pressed", String(kindle));
      if (kindle && halamanGlobal && halamanGlobal !== "cover" && halamanGlobal !== "daftar-isi" && !kunciGlobal) {
        halamanGlobal = null;
        if (suratData) {
          aktif = suratData.nomor;
          tandaiAktif();
        }
      }
      if (modeViewer === "xtc" && halamanGlobal && !kunciGlobal) {
        halamanGlobal = null;
        if (suratData) {
          aktif = suratData.nomor;
          tandaiAktif();
        }
      }
      if (!ganti) {
        return;
      }
      pakaiTata();
      if (suratData || (kindle && halamanGlobal)) {
        tampilkanHalaman();
        if (ayat && !kindle) {
          lompatKeAyat(ayat);
        }
      } else if ((modeViewer === "xtc" || kindle) && !kunciGlobal) {
        pageBody.classList.remove("is-cover", "is-penutup");
        pageBody.replaceChildren();
        const status = document.createElement("p");
        status.className = "status";
        status.textContent = antarmukaInggris() ? "Choose a surah to load verses." : "Pilih surat untuk memuat ayat.";
        pageBody.append(status);
        aturChevron();
      }
      tandaiHalamanGlobal();
    }

    async function denganMode(mode, tugas) {
      const asalMode = modeViewer;
      const asalIndeks = indeks;
      kunciGlobal = true;
      aturModeViewer(mode, { paksa: true });
      try {
        return await tugas();
      } finally {
        indeks = asalIndeks;
        aturModeViewer(asalMode, { paksa: true });
        kunciGlobal = false;
        if (halamanGlobal && modeViewer === "kindle") {
          tampilkanKindle();
        } else if (halamanGlobal && halamanTampil()) {
          tampilkanHalamanGlobal();
        }
      }
    }

    function tombolEkspor() {
      return Array.from(document.querySelectorAll(".export-btn"));
    }

    function kunciEkspor(kunci) {
      tombolEkspor().forEach((btn) => {
        btn.disabled = kunci;
      });
      modeXtcBtn.disabled = kunci;
      modeEpubBtn.disabled = kunci;
      modeKindleBtn.disabled = kunci;
    }

    async function eksporXtc(tombol) {
      if (!suratData || tombol.disabled) {
        return;
      }
      const label = tombol.textContent;
      kunciEkspor(true);
      let gagal = false;
      try {
        await denganMode("xtc", async () => {
          const berkas = [];
          indeks = 0;
          while (true) {
            tampilkanHalaman();
            const list = daftarHalaman();
            tombol.textContent = "EXPORT XTEINK " + (indeks + 1) + "/" + list.length;
            berkas.push(bitmapXtg(await gambarLayar()));
            if (indeks >= list.length - 1) {
              break;
            }
            indeks += 1;
          }
          const file = bangunXtc(berkas);
          unduhBlob(new Blob([file], { type: "application/octet-stream" }), namaBerkasXtc());
        });
      } catch (error) {
        gagal = true;
        tombol.textContent = "EXPORT XTEINK gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "EXPORT XTEINK gagal") {
            tombol.textContent = label;
          }
        }, 2500);
      } finally {
        kunciEkspor(false);
        if (!gagal) {
          tombol.textContent = label;
        }
      }
    }

    let jszipSiap = null;

    function muatJszip() {
      if (window.JSZip) {
        return Promise.resolve(window.JSZip);
      }
      if (!jszipSiap) {
        jszipSiap = new Promise((resolve, reject) => {
          const skrip = document.createElement("script");
          skrip.src = "https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js";
          skrip.onload = () => resolve(window.JSZip);
          skrip.onerror = () => reject(new Error("Gagal memuat pembuat EPUB."));
          document.head.append(skrip);
        });
      }
      return jszipSiap;
    }

    let fontEpubSiap = null;

    const FONT_EPUB = [
      { keluarga: "Lato", berkas: "Lato-Regular.ttf", weight: 400, style: "normal" },
      { keluarga: "Lato", berkas: "Lato-Italic.ttf", weight: 400, style: "italic" },
      { keluarga: "Lato", berkas: "Lato-Bold.ttf", weight: 700, style: "normal" },
      { keluarga: "Noto Sans Arabic Light", berkas: "NotoSansArabic-Light.ttf", weight: 400, style: "normal" },
      { keluarga: "Noto Sans Arabic", berkas: "NotoSansArabic-Regular.ttf", weight: 400, style: "normal" },
      { keluarga: "Noto Sans Arabic", berkas: "NotoSansArabic-Bold.ttf", weight: 700, style: "normal" },
      { keluarga: "Noto Kufi Arabic", berkas: "NotoKufiArabic-Bold.ttf", weight: 700, style: "normal" },
      { keluarga: "Scheherazade New", berkas: "ScheherazadeNew-Regular.ttf", weight: 400, style: "normal" },
    ];

    function bobotFontEpub(weight) {
      if (weight === 700) {
        return ["bold", "700"];
      }
      if (weight === 400) {
        return ["normal", "400"];
      }
      return [String(weight)];
    }

    function aturanFontEpub(font) {
      return bobotFontEpub(font.weight).map((weight) => [
        "@font-face {",
        '  font-family: "' + font.keluarga + '";',
        "  font-style: " + font.style + ";",
        "  font-weight: " + weight + ";",
        "  src: url(fonts/" + font.berkas + ");",
        "}",
      ].join("\n")).join("\n");
    }

    function fontTtfUtuh(data) {
      if (data.length < 1024) {
        return false;
      }
      const tt = data[0] === 0x00 && data[1] === 0x01 && data[2] === 0x00 && data[3] === 0x00;
      const trueTag = data[0] === 0x74 && data[1] === 0x72 && data[2] === 0x75 && data[3] === 0x65;
      const otf = data[0] === 0x4f && data[1] === 0x54 && data[2] === 0x54 && data[3] === 0x4f;
      return tt || trueTag || otf;
    }

    function muatFontEpub() {
      if (!fontEpubSiap) {
        fontEpubSiap = (async () => {
          const berkas = await Promise.all(FONT_EPUB.map(async (font) => {
            const res = await fetch("font/" + font.berkas);
            if (!res.ok) {
              throw new Error("Font lokal tidak ditemukan: " + font.berkas);
            }
            const data = new Uint8Array(await res.arrayBuffer());
            if (!fontTtfUtuh(data)) {
              throw new Error("Berkas font tidak valid: " + font.berkas);
            }
            return { nama: font.berkas, mime: "font/ttf", data };
          }));
          const css = FONT_EPUB.map(aturanFontEpub).join("\n");
          return { css, berkas };
        })();
      }
      return fontEpubSiap;
    }

    function simpanFontEpub(zip, berkas) {
      if (!berkas.length) {
        throw new Error("Font EPUB tidak tertanam.");
      }
      for (const font of berkas) {
        if (!font.data || !font.data.byteLength) {
          throw new Error("Font kosong: " + font.nama);
        }
        zip.file("OEBPS/fonts/" + font.nama, font.data, {
          compression: "DEFLATE",
          compressionOptions: { level: 9 },
        });
      }
    }

    function escapeXml(nilai) {
      return String(nilai ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&apos;");
    }

    function htmlKeXhtml(html) {
      return String(html)
        .replace(/\s+([a-zA-Z:-]+)=""/g, "")
        .replace(/<(img|meta|br|hr|link|input)([^>]*)>/gi, (m, tag, attrs) => {
          if (/\s*\/>$/.test(m)) {
            return m;
          }
          return "<" + tag + attrs + " />";
        });
    }

    const EPUB_LEBAR = 1072;
    const EPUB_TINGGI = 1448;

    function gayaEpub() {
      return [
        "* { box-sizing: border-box; }",
        "@page { size: 107.2mm 144.8mm; margin: 0; }",
        "html, body { margin: 0; padding: 0; width: " + EPUB_LEBAR + "px; height: " + EPUB_TINGGI + "px; }",
        'body { color: #111111; background: #ffffff; font-family: "Lato", sans-serif; font-weight: normal; font-style: normal; }',
        ".page-body { width: " + EPUB_LEBAR + "px; height: " + EPUB_TINGGI + "px; padding: 60px 100px 140px; overflow: hidden; position: relative; }",
        ".page-body.is-cover { padding: 0; background: #ffffff; overflow: hidden; }",
        ".page-body.is-penutup { display: flex; align-items: center; justify-content: center; padding: 0; }",
        ".nomor-halaman { position: absolute; left: 0; right: 0; bottom: 60px; margin: 0; text-align: center; font-size: 28px; font-weight: 500; }",
        ".nama-surah { margin: 0 0 30px; font-size: 28px; line-height: 1.35; text-align: right; }",
        ".ayat { padding: 20px 0; border-top: 1px solid #cfcfcf; }",
        ".ayat-top { display: flex; justify-content: flex-end; margin-bottom: 30px; }",
        ".ayat-no { font-size: 28px; font-weight: 500; color: #444444; }",
        '.teks-arab { margin: 0 0 30px; font-family: "Scheherazade New", serif; font-size: 50px; font-weight: normal; font-style: normal; line-height: 1.9; text-align: right; }',
        ".ayat-no-arab { font-size: 70%; }",
        ".teks-latin, .teks-id { margin: 0; font-size: 29px; line-height: 1.4; text-align: left; }",
        '.teks-latin { font-family: "Lato", sans-serif; font-style: italic; font-weight: normal; }',
        ".teks-id { margin-top: 25px; }",
        ".teks-id[lang=\"ja\"], .cover-sumber[lang=\"ja\"] { font-family: \"Hiragino Sans\", \"Yu Gothic\", \"Noto Sans JP\", sans-serif; }",
        '.penutup-arab { margin: 0; font-family: "Scheherazade New", serif; font-size: 56px; font-weight: normal; font-style: normal; line-height: 2.1; text-align: center; }',
        '.daftar-isi { color: #111111; font-family: "Lato", sans-serif; }',
        ".daftar-isi h2 { margin: 0 0 40px; font-size: 40px; font-weight: bold; text-align: left; }",
        ".daftar-isi-grid { display: grid; grid-template-columns: 1fr 1fr; column-gap: 40px; row-gap: 30px; }",
        ".isi-item, a.isi-item { display: grid; grid-template-columns: 90px minmax(0, 1fr); gap: 20px; align-items: center; color: inherit; text-decoration: none; }",
        ".isi-nomor { width: 90px; height: 90px; display: grid; place-items: center; border-radius: 16px; background: #e4e4e4; color: #111111; font-size: 28px; font-weight: 500; }",
        ".isi-nama, .isi-meta { display: block; color: #111111; line-height: 1.25; }",
        ".isi-nama { font-size: 30px; font-weight: bold; }",
        ".isi-meta { margin-top: 4px; font-size: 24px; font-weight: 400; }",
        '.cover { position: relative; width: 100%; height: 100%; overflow: hidden; color: #000000; text-align: right; font-family: "Noto Sans Arabic", sans-serif; }',
        ".cover.is-tengah { display: flex; flex-direction: column; justify-content: center; align-items: flex-end; padding: 0 108px; gap: 20px; }",
        ".cover.is-tengah p { position: static; right: auto; margin: 0; }",
        '.cover-arab { font-family: "Noto Kufi Arabic", sans-serif; font-size: 108px; font-weight: bold; line-height: 1.1; white-space: nowrap; }',
        ".cover-nama { font-size: 50px; font-weight: bold; line-height: 1.3; white-space: nowrap; }",
        '.cover-arti { font-family: "Noto Sans Arabic Light", "Noto Sans Arabic", sans-serif; font-size: 40px; font-weight: normal; line-height: 1.3; white-space: nowrap; }',
        ".cover-meta { font-size: 32px; font-weight: bold; line-height: 1.4; white-space: nowrap; }",
      ].join("\n");
    }

    async function gambarCoverEpub() {
      const asal = indeks;
      indeks = 0;
      tampilkanHalaman();
      try {
        const ukuran = ukuranHalaman();
        const canvas = await renderHalaman({
          ...ukuran,
          scale: EPUB_LEBAR / ukuran.width,
          backgroundColor: "#ffffff",
        });
        const blob = await new Promise((resolve, reject) => {
          canvas.toBlob((hasil) => {
            if (hasil) {
              resolve(hasil);
            } else {
              reject(new Error("Cover gagal dibuat."));
            }
          }, "image/png");
        });
        return blob;
      } finally {
        indeks = asal;
        tampilkanHalaman();
      }
    }

    function xhtmlGambar(judul, src) {
      return [
        "<" + "?xml version=\"1.0\" encoding=\"UTF-8\"?>",
        '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="id" xml:lang="id">',
        "<head>",
        '<meta charset="utf-8" />',
        "<title>" + escapeXml(judul) + "</title>",
        '<meta name="viewport" content="width=' + EPUB_LEBAR + ', height=' + EPUB_TINGGI + '" />',
        "<style>html,body{margin:0;padding:0;width:" + EPUB_LEBAR + "px;height:" + EPUB_TINGGI + "px;background:#ffffff;}img{display:block;width:" + EPUB_LEBAR + "px;height:" + EPUB_TINGGI + "px;object-fit:cover;}</style>",
        "</head>",
        '<body><img src="' + src + '" alt="' + escapeXml(judul) + '" width="' + EPUB_LEBAR + '" height="' + EPUB_TINGGI + '" /></body>',
        "</html>",
      ].join("\n");
    }

    function xhtmlCover(judul) {
      return xhtmlGambar(judul, "cover.png");
    }

    function xhtmlHalaman(judul, isi, kelas) {
      return [
        "<" + "?xml version=\"1.0\" encoding=\"UTF-8\"?>",
        '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="id" xml:lang="id">',
        "<head>",
        '<meta charset="utf-8" />',
        "<title>" + escapeXml(judul) + "</title>",
        '<meta name="viewport" content="width=' + EPUB_LEBAR + ', height=' + EPUB_TINGGI + '" />',
        '<link rel="stylesheet" href="gaya.css" type="text/css" />',
        "</head>",
        '<body><div class="' + kelas + '">' + htmlKeXhtml(isi) + "</div></body>",
        "</html>",
      ].join("\n");
    }

    function navEpub(judul, halaman) {
      const isi = halaman.map((item, i) => {
        const nama = item.nama || (i === 0 ? "Sampul" : "Halaman " + i);
        return '<li><a href="' + item.berkas + '">' + escapeXml(nama) + "</a></li>";
      }).join("");
      return [
        "<" + "?xml version=\"1.0\" encoding=\"UTF-8\"?>",
        '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="id" xml:lang="id">',
        "<head><title>Daftar isi</title><meta charset=\"utf-8\" /></head>",
        '<body><nav epub:type="toc" id="toc"><h1>' + escapeXml(judul) + "</h1><ol>" + isi + "</ol></nav></body></html>",
      ].join("\n");
    }

    function idPublikasi() {
      const bytes = new Uint8Array(16);
      if (crypto.getRandomValues) {
        crypto.getRandomValues(bytes);
      } else {
        for (let i = 0; i < bytes.length; i += 1) {
          bytes[i] = Math.floor(Math.random() * 256);
        }
      }
      bytes[6] = (bytes[6] & 0x0f) | 0x40;
      bytes[8] = (bytes[8] & 0x3f) | 0x80;
      const hex = Array.from(bytes, (b) => b.toString(16).padStart(2, "0")).join("");
      return hex.slice(0, 8) + "-" + hex.slice(8, 12) + "-" + hex.slice(12, 16) + "-" + hex.slice(16, 20) + "-" + hex.slice(20);
    }

    function cssEpub(fontCss) {
      return (fontCss + "\n" + gayaEpub()).replace(/direction\s*:\s*[^;}"']+;?/gi, "");
    }

    async function berkasGambar(blob, nama) {
      const kepala = new Uint8Array(await blob.slice(0, 3).arrayBuffer());
      const jpeg = kepala[0] === 0xff && kepala[1] === 0xd8 && kepala[2] === 0xff;
      return {
        href: nama + (jpeg ? ".jpg" : ".png"),
        mime: jpeg ? "image/jpeg" : "image/png",
        blob,
      };
    }

    function opfEpub(meta, halaman, font, tambahan, sampul) {
      const item = halaman.map((h) =>
        '<item id="' + h.id + '" href="' + h.berkas + '" media-type="application/xhtml+xml"/>'
      ).join("\n    ");
      const itemFont = (font || []).map((f, i) =>
        '<item id="font' + (i + 1) + '" href="fonts/' + f.nama + '" media-type="' + f.mime + '"/>'
      ).join("\n    ");
      const spine = halaman.map((h) => '<itemref idref="' + h.id + '"/>').join("\n    ");
      return [
        "<" + "?xml version=\"1.0\" encoding=\"UTF-8\"?>",
        '<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="pub-id" version="3.2" xml:lang="id" prefix="ibooks: http://vocabulary.itunes.apple.com/rdf/ibooks/vocabulary-extensions-1.0/">',
        "<metadata xmlns:dc=\"http://purl.org/dc/elements/1.1/\">",
        '<dc:identifier id="pub-id">' + escapeXml(meta.id) + "</dc:identifier>",
        "<dc:title>" + escapeXml(meta.judul) + "</dc:title>",
        "<dc:language>id</dc:language>",
        "<dc:creator>Kementerian Agama Republik Indonesia</dc:creator>",
        '<meta property="ibooks:specified-fonts">true</meta>',
        '<meta property="dcterms:modified">' + meta.diubah + "</meta>",
        '<meta property="rendition:layout">pre-paginated</meta>',
        '<meta property="rendition:orientation">portrait</meta>',
        '<meta property="rendition:spread">none</meta>',
        '<meta name="cover" content="cover-img"/>',
        "</metadata>",
        "<manifest>",
        '<item id="nav" href="nav.xhtml" media-type="application/xhtml+xml" properties="nav"/>',
        '<item id="css" href="gaya.css" media-type="text/css"/>',
        '<item id="cover-img" href="' + (sampul?.href || "cover.png") + '" media-type="' + (sampul?.mime || "image/png") + '" properties="cover-image"/>',
        tambahan ? "    " + tambahan : "",
        itemFont ? "    " + itemFont : "",
        "    " + item,
        "</manifest>",
        "<spine>",
        "    " + spine,
        "</spine>",
        "</package>",
      ].join("\n");
    }

    function containerEpub() {
      return [
        "<" + "?xml version=\"1.0\" encoding=\"UTF-8\"?>",
        '<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">',
        "<rootfiles><rootfile full-path=\"OEBPS/package.opf\" media-type=\"application/oebps-package+xml\"/></rootfiles>",
        "</container>",
      ].join("\n");
    }

    function unduhBlob(blob, nama) {
      const url = URL.createObjectURL(blob);
      const unduh = document.createElement("a");
      unduh.href = url;
      unduh.download = nama;
      unduh.click();
      // Safari/Firefox butuh URL tetap hidup sebentar setelah click() untuk berkas besar.
      window.setTimeout(() => URL.revokeObjectURL(url), 60000);
    }

    function namaBerkasEpub() {
      const nomor = String(suratData.nomor).padStart(3, "0");
      const latin = (suratData.namaLatin || "surat").replace(/[^\w.-]+/g, "-");
      return nomor + "-" + latin + ".epub";
    }

    function bahasaKindle() {
      if (bahasa === "en") {
        return "en";
      }
      if (bahasa === "ja") {
        return "ja";
      }
      return "id";
    }

    function xmlKindle(isi) {
      return "<" + "?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" + isi;
    }

    function gayaKindle() {
      return [
        "@page { margin-top: 1.2em; margin-bottom: 1.2em; }",
        "body { margin: 0; color: #111111; font-family: Bookerly, Palatino, Georgia, serif; font-weight: normal; font-style: normal; line-height: 1.45; }",
        "h1 { margin: 0 0 0.3em; font-family: \"Amazon Ember\", Helvetica, sans-serif; font-size: 1.4em; font-weight: bold; }",
        ".nama-arab, .arab { font-family: Bookerly, serif; }",
        ".nama-arab { margin: 0 0 0.2em; font-weight: bold; font-size: 2em; text-align: right; }",
        ".meta { margin: 0 0 1.6em; }",
        "section { margin: 0 0 1.2em; padding: 0 0 0.8em; border-bottom: 1px solid #cccccc; break-inside: avoid; page-break-inside: avoid; }",
        "section:last-child { margin-bottom: 0; border-bottom: 0; }",
        ".arab { margin: 0 0 0.3em; font-weight: normal; font-size: 1.6em; line-height: 1.9; text-align: right; }",
        ".latin { margin: 0; font-style: italic; }",
        ".makna { margin: 0.3em 0 0; }",
        ".sampul { margin: 0; text-align: center; }",
        ".sampul img { width: 100%; height: auto; }",
        "nav#toc ol { margin: 0.4em 0 0; padding: 0; list-style: none; }",
        "nav#toc li { margin: 0; padding: 0.45em 0; border-bottom: 1px solid #cccccc; page-break-inside: avoid; }",
        "nav#toc a { color: inherit; text-decoration: none; }",
      ].join("\n");
    }

    function xhtmlDokumenKindle(judul, isi, tipe) {
      const kode = bahasaKindle();
      return xmlKindle([
        '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="' + kode + '" xml:lang="' + kode + '">',
        "<head>",
        '<meta charset="utf-8"/>',
        "<title>" + escapeXml(judul) + "</title>",
        '<link rel="stylesheet" type="text/css" href="gaya.css"/>',
        "</head>",
        tipe ? '<body epub:type="' + tipe + '">' : "<body>",
        isi,
        "</body>",
        "</html>",
      ].join("\n"));
    }

    function xhtmlAyatKindle(ayat) {
      const arab = escapeXml(teks(ayat.teksArab));
      const latin = escapeXml(teks(teksLatinBahasa(ayat)));
      const makna = escapeXml(teks(teksTerjemahan(ayat)));
      return [
        "<section>",
        '<p class="arab" lang="ar" dir="rtl">' + arab + ' <span dir="ltr">(' + angkaArab(ayat.nomorAyat) + ")</span></p>",
        latin ? '<p class="latin">' + latin + "</p>" : "",
        '<p class="makna">(' + ayat.nomorAyat + ") " + makna + "</p>",
        "</section>",
      ].filter(Boolean).join("\n");
    }

    function xhtmlSurahKindle(data, ayat) {
      const judul = data.nomor + ". " + (data.namaLatin || "");
      const kepala = [
        '<p class="nama-arab" lang="ar" dir="rtl">' + escapeXml(data.nama || "") + "</p>",
        "<h1>" + escapeXml(judul) + "</h1>",
        '<p class="meta">' + escapeXml((data.arti || "") + " · " + data.jumlahAyat + " ayat · " + (data.tempatTurun || "")) + "</p>",
      ].join("\n");
      const isi = (ayat || []).map(xhtmlAyatKindle).join("\n");
      return xhtmlDokumenKindle(judul, kepala + "\n" + isi);
    }

    function navKindle(judul, dokumen) {
      const kode = bahasaKindle();
      const surah = dokumen.filter((item) => /^s\d+\.xhtml$/.test(item.berkas));
      const isi = surah.map((item) => '<li><a href="' + item.berkas + '">' + escapeXml(item.nama) + "</a></li>").join("");
      const awal = surah[0];
      const tanda = [];
      if (dokumen.some((item) => item.id === "cover")) {
        tanda.push('<li><a epub:type="cover" href="cover.xhtml">Sampul</a></li>');
      }
      if (dokumen.some((item) => item.id === "daftar")) {
        tanda.push('<li><a epub:type="toc" href="daftar.xhtml">Daftar isi</a></li>');
      }
      if (awal) {
        tanda.push('<li><a epub:type="bodymatter" href="' + awal.berkas + '">' + escapeXml(awal.nama) + "</a></li>");
      }
      const landmarks = tanda.length
        ? '<nav epub:type="landmarks" id="landmarks" hidden="hidden"><ol>' + tanda.join("") + "</ol></nav>"
        : "";
      return xmlKindle([
        '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops" lang="' + kode + '" xml:lang="' + kode + '">',
        "<head><title>" + escapeXml(judul) + "</title><meta charset=\"utf-8\"/></head>",
        "<body>",
        '<nav epub:type="toc" id="toc"><h1>' + escapeXml(judul) + "</h1><ol>" + isi + "</ol></nav>",
        landmarks,
        "</body>",
        "</html>",
      ].join("\n"));
    }

    function panduanKindle(dokumen) {
      const cover = dokumen.find((item) => item.id === "cover");
      const daftar = dokumen.find((item) => item.id === "daftar");
      const teks = dokumen.find((item) => /^s\d+\.xhtml$/.test(item.berkas));
      const rujukan = [];
      if (cover) {
        rujukan.push('<reference type="cover" title="Sampul" href="' + cover.berkas + '"/>');
      }
      if (daftar) {
        rujukan.push('<reference type="toc" title="Daftar isi" href="' + daftar.berkas + '"/>');
      }
      if (teks) {
        rujukan.push('<reference type="text" title="' + escapeXml(teks.nama) + '" href="' + teks.berkas + '"/>');
      }
      return rujukan.length ? "<guide>\n    " + rujukan.join("\n    ") + "\n  </guide>" : "";
    }

    function opfKindle(meta, dokumen, sampul) {
      const kode = bahasaKindle();
      const item = dokumen.map((h) =>
        '<item id="' + h.id + '" href="' + h.berkas + '" media-type="application/xhtml+xml"/>'
      ).join("\n    ");
      const spine = dokumen.map((h) => '<itemref idref="' + h.id + '"/>').join("\n    ");
      const barisSampul = sampul
        ? '<item id="cover-img" href="' + sampul + '" media-type="' + (String(sampul).endsWith(".jpg") ? "image/jpeg" : "image/png") + '" properties="cover-image"/>'
        : "";
      return xmlKindle([
        '<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="pub-id" version="3.0" xml:lang="' + kode + '">',
        '<metadata xmlns:dc="http://purl.org/dc/elements/1.1/">',
        '<dc:identifier id="pub-id">' + escapeXml(meta.id) + "</dc:identifier>",
        "<dc:title>" + escapeXml(meta.judul) + "</dc:title>",
        "<dc:language>" + kode + "</dc:language>",
        "<dc:creator>Kementerian Agama Republik Indonesia</dc:creator>",
        '<meta property="dcterms:modified">' + meta.diubah + "</meta>",
        sampul ? '<meta name="cover" content="cover-img"/>' : "",
        "</metadata>",
        "<manifest>",
        '<item id="nav" href="nav.xhtml" media-type="application/xhtml+xml" properties="nav"/>',
        '<item id="css" href="gaya.css" media-type="text/css"/>',
        barisSampul,
        item,
        "</manifest>",
        "<spine>",
        spine,
        "</spine>",
        panduanKindle(dokumen),
        "</package>",
      ].filter((baris) => baris !== "").join("\n"));
    }

    async function gambarHitamPutih(blob) {
      const url = URL.createObjectURL(blob);
      try {
        const img = await new Promise((resolve, reject) => {
          const el = new Image();
          el.onload = () => resolve(el);
          el.onerror = () => reject(new Error("Sampul gagal dibaca."));
          el.src = url;
        });
        const canvas = document.createElement("canvas");
        canvas.width = img.naturalWidth;
        canvas.height = img.naturalHeight;
        const ctx = canvas.getContext("2d", { willReadFrequently: true });
        ctx.drawImage(img, 0, 0);
        const data = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const px = data.data;
        for (let i = 0; i < px.length; i += 4) {
          const terang = px[i] * 0.299 + px[i + 1] * 0.587 + px[i + 2] * 0.114;
          px[i] = px[i + 1] = px[i + 2] = terang;
        }
        ctx.putImageData(data, 0, 0);
        return await new Promise((resolve, reject) => {
          canvas.toBlob((hasil) => {
            if (hasil) {
              resolve(hasil);
            } else {
              reject(new Error("Sampul hitam-putih gagal dibuat."));
            }
          }, "image/jpeg", 0.92);
        });
      } finally {
        URL.revokeObjectURL(url);
      }
    }

    function xhtmlSampulKindle(src, judul) {
      return xhtmlDokumenKindle(judul, '<p class="sampul"><img src="' + src + '" alt="' + escapeXml(judul) + '"/></p>', "cover");
    }

    function xhtmlDaftarKindle(entri) {
      const isi = entri.map((item) => {
        const baris = item.nomor + ". " + item.namaLatin + " — " + (item.arti || "") + " · " + item.jumlahAyat + " ayat · " + (item.tempatTurun || "");
        return '<li><a href="' + item.berkas + '">' + escapeXml(baris) + "</a></li>";
      }).join("");
      return xhtmlDokumenKindle("Daftar isi", '<nav epub:type="toc" id="toc"><h1>Daftar isi</h1><ol>' + isi + "</ol></nav>", "toc");
    }

    async function zipKindle(dokumen, judul, namaBerkas, sampul) {
      const JSZip = await muatJszip();
      const zip = new JSZip();
      zip.file("mimetype", "application/epub+zip", { compression: "STORE" });
      if (sampul) {
        zip.file("OEBPS/cover.jpg", sampul);
      }
      dokumen.forEach((dok) => {
        zip.file("OEBPS/" + dok.berkas, dok.xhtml);
      });
      const meta = {
        id: "urn:uuid:" + idPublikasi(),
        judul,
        diubah: new Date().toISOString().replace(/\.\d{3}Z$/, "Z"),
      };
      zip.file("META-INF/container.xml", containerEpub());
      zip.file("OEBPS/gaya.css", gayaKindle());
      zip.file("OEBPS/nav.xhtml", navKindle(judul, dokumen));
      zip.file("OEBPS/package.opf", opfKindle(meta, dokumen, sampul ? "cover.jpg" : ""));
      const blob = await zip.generateAsync({
        type: "blob",
        mimeType: "application/epub+zip",
        compression: "DEFLATE",
        compressionOptions: { level: 9 },
      });
      unduhBlob(blob, namaBerkas);
    }

    async function eksporKindle(tombol) {
      if (!suratData || tombol.disabled) {
        return;
      }
      const label = tombol.textContent;
      kunciEkspor(true);
      let gagal = false;
      try {
        const id = "s" + String(suratData.nomor).padStart(3, "0");
        await zipKindle(
          [{
            id,
            berkas: id + ".xhtml",
            nama: suratData.nomor + ". " + suratData.namaLatin,
            xhtml: xhtmlSurahKindle(suratData, ayatList),
          }],
          suratData.nomor + ". " + suratData.namaLatin + " — " + suratData.nama,
          namaBerkasEpub().replace(/\.epub$/, "-kindle.epub")
        );
      } catch (error) {
        gagal = true;
        tombol.textContent = "EXPORT KINDLE gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "EXPORT KINDLE gagal") {
            tombol.textContent = label;
          }
        }, 2500);
      } finally {
        kunciEkspor(false);
        if (!gagal) {
          tombol.textContent = label;
        }
      }
    }

    async function eksporQuranKindle(tombol) {
      if (!surat.length || tombol.disabled) {
        return;
      }
      const label = tombol.textContent;
      kunciEkspor(true);
      let gagal = false;
      try {
        tombol.textContent = "Memuat surat…";
        const semua = await ambil("/semua");
        const surah = semua.map((data) => {
          const id = "s" + String(data.nomor).padStart(3, "0");
          return {
            id,
            berkas: id + ".xhtml",
            nama: data.nomor + ". " + data.namaLatin,
            xhtml: xhtmlSurahKindle(data, data.ayat || []),
          };
        });
        const daftar = xhtmlDaftarKindle(semua.map((data, indeks) => ({
          nomor: data.nomor,
          namaLatin: data.namaLatin,
          arti: data.arti,
          jumlahAyat: data.jumlahAyat,
          tempatTurun: data.tempatTurun,
          berkas: surah[indeks].berkas,
        })));
        const dokumen = [
          { id: "cover", berkas: "cover.xhtml", nama: "Sampul", xhtml: xhtmlSampulKindle("cover.jpg", "Al-Qur'an") },
          { id: "daftar", berkas: "daftar.xhtml", nama: "Daftar isi", xhtml: daftar },
          ...surah,
        ];
        tombol.textContent = "Menulis Kindle…";
        const sampul = await gambarHitamPutih(await ambilGambar("gambar/halaman-1-epub.png?v=3"));
        await zipKindle(dokumen, "Al-Qur'an", "al-quran-kindle.epub", sampul);
      } catch (error) {
        gagal = true;
        tombol.textContent = "EXPORT KINDLE gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "EXPORT KINDLE gagal") {
            tombol.textContent = label;
          }
        }, 2500);
      } finally {
        kunciEkspor(false);
        if (!gagal) {
          tombol.textContent = label;
        }
      }
    }

    async function eksporEpub(tombol) {
      if (!suratData || tombol.disabled) {
        return;
      }
      const label = tombol.textContent;
      kunciEkspor(true);
      let gagal = false;
      try {
        const JSZip = await muatJszip();
        const fontEpub = await muatFontEpub();
        await denganMode("epub", async () => {
          const zip = new JSZip();
          zip.file("mimetype", "application/epub+zip", { compression: "STORE" });
          simpanFontEpub(zip, fontEpub.berkas);
          const halaman = [];
          tombol.textContent = "EXPORT EPUB cover";
          zip.file("OEBPS/cover.png", await gambarCoverEpub());
          zip.file("OEBPS/p001.xhtml", xhtmlCover(suratData.namaLatin + " — sampul"));
          halaman.push({ id: "p001", berkas: "p001.xhtml", nama: "Sampul" });
          indeks = 1;
          while (true) {
            tampilkanHalaman();
            const list = daftarHalaman();
            if (indeks >= list.length) {
              break;
            }
            tombol.textContent = "EXPORT EPUB " + (indeks + 1) + "/" + list.length;
            const id = "p" + String(indeks + 1).padStart(3, "0");
            const berkas = id + ".xhtml";
            const kelas = pageBody.className || "page-body";
            const judul = suratData.namaLatin + " — " + indeks;
            const nama = "Halaman " + indeks;
            zip.file("OEBPS/" + berkas, xhtmlHalaman(judul, pageBody.innerHTML, kelas));
            halaman.push({ id, berkas, nama });
            if (indeks >= list.length - 1) {
              break;
            }
            indeks += 1;
          }
          const meta = {
            id: "urn:uuid:" + idPublikasi(),
            judul: suratData.nomor + ". " + suratData.namaLatin + " — " + suratData.nama,
            diubah: new Date().toISOString().replace(/\.\d{3}Z$/, "Z"),
          };
          zip.file("META-INF/container.xml", containerEpub());
          zip.file("OEBPS/gaya.css", cssEpub(fontEpub.css));
          zip.file("OEBPS/nav.xhtml", navEpub(suratData.namaLatin, halaman));
          zip.file("OEBPS/package.opf", opfEpub(meta, halaman, fontEpub.berkas));
          const blob = await zip.generateAsync({
            type: "blob",
            mimeType: "application/epub+zip",
            compression: "DEFLATE",
            compressionOptions: { level: 9 },
          });
          unduhBlob(blob, namaBerkasEpub());
        });
      } catch (error) {
        gagal = true;
        tombol.textContent = "EXPORT EPUB gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "EXPORT EPUB gagal") {
            tombol.textContent = label;
          }
        }, 2500);
      } finally {
        kunciEkspor(false);
        if (!gagal) {
          tombol.textContent = label;
        }
      }
    }

    function idBerkas(nomor) {
      const id = "p" + String(nomor).padStart(4, "0");
      return { id, berkas: id + ".xhtml" };
    }

    function stempelNomor(html, sekarang, total) {
      const teks = sekarang + " / " + total;
      if (html.includes('class="nomor-halaman"')) {
        return html.replace(/(class="nomor-halaman">)[^<]*/, "$1" + teks);
      }
      return html.replace("</div>", '<p class="nomor-halaman">' + teks + "</p></div>");
    }

    function awalDariJumlah(jumlahDaftar, jumlahHalaman) {
      const awal = [];
      let kursor = jumlahDaftar + 1;
      for (const jumlah of jumlahHalaman) {
        awal.push(kursor);
        kursor += jumlah;
      }
      return awal;
    }

    async function ambilGambar(url) {
      const res = await fetch(url);
      if (!res.ok) {
        throw new Error("Gambar EPUB tidak ditemukan.");
      }
      return res.blob();
    }

    async function eksporQuran(tombol) {
      if (!surat.length || tombol.disabled) {
        return;
      }
      const label = tombol.textContent;
      const asal = {
        suratData,
        ayatList,
        pecahan,
        halamanCache,
        indeks,
        halamanGlobal,
        lembarGlobal,
        pecahanDaftar,
        aktif,
        kunciTata,
      };
      kunciEkspor(true);
      let gagal = false;
      try {
        const JSZip = await muatJszip();
        const fontEpub = await muatFontEpub();
        await denganMode("epub", async () => {
          try {
          tombol.textContent = "Memuat surat…";
          const semua = await ambil("/semua");
          const isiSurah = [];
          for (let i = 0; i < semua.length; i += 1) {
            const data = semua[i];
            tombol.textContent = "Menyusun " + data.namaLatin;
            simpanTata();
            suratData = data;
            ayatList = data.ayat || [];
            pakaiTata();
            const list = daftarHalaman();
            const halamanSurah = [];
            for (let posisi = 0; posisi < list.length; posisi += 1) {
              indeks = posisi;
              tampilkanHalaman();
              if (posisi === 0 && !pageBody.querySelector(".nomor-halaman")) {
                const nomor = document.createElement("p");
                nomor.className = "nomor-halaman";
                nomor.textContent = "0 / 0";
                pageBody.append(nomor);
              }
              halamanSurah.push({
                html: pageBody.innerHTML,
                kelas: pageBody.className || "page-body",
                judul: data.namaLatin + (posisi === 0 ? " — sampul" : " — " + posisi),
              });
            }
            isiSurah.push({ data, halaman: halamanSurah });
            await new Promise((resolve) => window.setTimeout(resolve, 0));
          }
          const jumlahHalaman = isiSurah.map((item) => item.halaman.length);
          let jumlahDaftar = 1;
          let potongan = [];
          let awal = [];
          for (let ulang = 0; ulang < 6; ulang += 1) {
            awal = awalDariJumlah(jumlahDaftar, jumlahHalaman);
            const entri = surat.map((item, indeksSurah) => ({
              ...item,
              halamanAwal: awal[indeksSurah],
              berkas: idBerkas(2 + jumlahDaftar + jumlahHalaman.slice(0, indeksSurah).reduce((jumlah, nilai) => jumlah + nilai, 0) + 1).berkas,
            }));
            potongan = susunDaftar(entri);
            if (potongan.length === jumlahDaftar) {
              break;
            }
            jumlahDaftar = potongan.length;
          }
          const total = jumlahDaftar + jumlahHalaman.reduce((jumlah, nilai) => jumlah + nilai, 0) + 1;
          const zip = new JSZip();
          zip.file("mimetype", "application/epub+zip", { compression: "STORE" });
          simpanFontEpub(zip, fontEpub.berkas);
          const gambarSampul = await berkasGambar(await ambilGambar("gambar/cover-epub.png?v=3"), "cover");
          const gambarPembuka = await berkasGambar(await ambilGambar("gambar/halaman-1-epub.png?v=3"), "halaman-1");
          zip.file("OEBPS/" + gambarSampul.href, gambarSampul.blob);
          zip.file("OEBPS/" + gambarPembuka.href, gambarPembuka.blob);
          const halaman = [];
          const nav = [];
          const sampul = idBerkas(1);
          zip.file("OEBPS/" + sampul.berkas, xhtmlGambar("Cover", gambarSampul.href));
          halaman.push({ ...sampul, nama: "Sampul" });
          nav.push({ ...sampul, nama: "Sampul" });
          const pembuka = idBerkas(2);
          zip.file("OEBPS/" + pembuka.berkas, xhtmlGambar("Halaman 1", gambarPembuka.href));
          halaman.push({ ...pembuka, nama: "Halaman 1" });
          nav.push({ ...pembuka, nama: "Halaman 1" });
          potongan.forEach((isi, lembar) => {
            gambarDaftarPotongan(isi, lembar === 0);
            const el = document.createElement("p");
            el.className = "nomor-halaman";
            el.textContent = (lembar + 1) + " / " + total;
            pageBody.append(el);
            const berkas = idBerkas(3 + lembar);
            zip.file("OEBPS/" + berkas.berkas, xhtmlHalaman("Daftar isi", pageBody.innerHTML, pageBody.className || "page-body"));
            halaman.push({ ...berkas, nama: lembar === 0 ? "Daftar isi" : "Daftar isi " + (lembar + 1) });
            if (lembar === 0) {
              nav.push({ ...berkas, nama: "Daftar isi" });
            }
          });
          let nomorBerkas = 2 + jumlahDaftar;
          let nomorHalaman = jumlahDaftar;
          isiSurah.forEach((item) => {
            nav.push({
              ...idBerkas(nomorBerkas + 1),
              nama: item.data.nomor + ". " + item.data.namaLatin,
            });
            item.halaman.forEach((hal) => {
              nomorBerkas += 1;
              nomorHalaman += 1;
              const berkas = idBerkas(nomorBerkas);
              zip.file(
                "OEBPS/" + berkas.berkas,
                xhtmlHalaman(hal.judul, stempelNomor(hal.html, nomorHalaman, total), hal.kelas)
              );
              halaman.push({ ...berkas, nama: item.data.namaLatin });
            });
          });
          gambarPenutup();
          const el = document.createElement("p");
          el.className = "nomor-halaman";
          el.textContent = total + " / " + total;
          pageBody.append(el);
          nomorBerkas += 1;
          const tutup = idBerkas(nomorBerkas);
          zip.file("OEBPS/" + tutup.berkas, xhtmlHalaman("Penutup", pageBody.innerHTML, pageBody.className || "page-body"));
          halaman.push({ ...tutup, nama: "Penutup" });
          nav.push({ ...tutup, nama: "Penutup" });
          const meta = {
            id: "urn:uuid:" + idPublikasi(),
            judul: "Al-Qur'an",
            diubah: new Date().toISOString().replace(/\.\d{3}Z$/, "Z"),
          };
          zip.file("META-INF/container.xml", containerEpub());
          zip.file("OEBPS/gaya.css", cssEpub(fontEpub.css));
          zip.file("OEBPS/nav.xhtml", navEpub("Al-Qur'an", nav));
          zip.file(
            "OEBPS/package.opf",
            opfEpub(
              meta,
              halaman,
              fontEpub.berkas,
              '<item id="halaman-1-img" href="' + gambarPembuka.href + '" media-type="' + gambarPembuka.mime + '"/>',
              { href: gambarSampul.href, mime: gambarSampul.mime }
            )
          );
          tombol.textContent = "Menulis EPUB…";
          const blob = await zip.generateAsync({
            type: "blob",
            mimeType: "application/epub+zip",
            compression: "DEFLATE",
            compressionOptions: { level: 9 },
          });
          unduhBlob(blob, "al-quran.epub");
          } finally {
            simpanTata();
            kunciTata = asal.kunciTata;
            suratData = asal.suratData;
            ayatList = asal.ayatList;
            pecahan = asal.pecahan;
            halamanCache = asal.halamanCache;
            halamanGlobal = asal.halamanGlobal;
            lembarGlobal = asal.lembarGlobal;
            pecahanDaftar = asal.pecahanDaftar;
            aktif = asal.aktif;
          }
        });
      } catch (error) {
        gagal = true;
        tombol.textContent = "EXPORT EPUB gagal";
        window.setTimeout(() => {
          if (tombol.textContent === "EXPORT EPUB gagal") {
            tombol.textContent = label;
          }
        }, 2500);
      } finally {
        kunciEkspor(false);
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
      const label = tombol.getAttribute("aria-label") || "Screenshot";
      tombol.disabled = true;
      tombol.setAttribute("aria-label", "Menyimpan screenshot");
      try {
        const canvas = await renderHalaman({
          ...ukuranHalaman(),
          scale: halamanTampil() ? 2 : 1,
          backgroundColor: halamanTampil() ? "#ffffff" : getComputedStyle(document.documentElement).getPropertyValue("--ink").trim(),
        });
        const blob = await new Promise((resolve) => canvas.toBlob(resolve, "image/jpeg", 0.92));
        if (!blob) {
          throw new Error("JPG gagal dibuat.");
        }
        unduhBlob(blob, namaScreenshot());
      } catch (error) {
        tombol.setAttribute("aria-label", "Screenshot gagal");
        window.setTimeout(() => {
          if (tombol.getAttribute("aria-label") === "Screenshot gagal") {
            tombol.setAttribute("aria-label", label);
          }
        }, 2500);
        return;
      } finally {
        tombol.disabled = false;
        if (tombol.getAttribute("aria-label") === "Menyimpan screenshot") {
          tombol.setAttribute("aria-label", label);
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
      if (!item || item.jenis === "cover" || item.jenis === "penutup" || item.jenis === "daftar-isi") {
        return false;
      }
      if (item.jenis === "grup") {
        return item.bagian.some((bagian) => bagian.ayat.nomorAyat === nomor);
      }
      return Boolean(item.ayat) && item.ayat.nomorAyat === nomor;
    }

    function tandaiHalamanDaftar() {
      paginasiEl.querySelectorAll("button").forEach((tombol) => {
        const aktif = Number(tombol.dataset.halaman) === halamanDaftar;
        if (aktif) {
          tombol.setAttribute("aria-current", "page");
        } else {
          tombol.removeAttribute("aria-current");
        }
      });
    }

    function lompatHalamanDaftar(halaman) {
      const li = daftarEl.querySelectorAll("li")[(halaman - 1) * UKURAN_SURAT];
      if (!li) {
        return;
      }
      halamanDaftar = halaman;
      tandaiHalamanDaftar();
      const atas = daftarEl.getBoundingClientRect().top;
      const tujuan = li.getBoundingClientRect().top - atas + daftarEl.scrollTop;
      kunciScrollDaftar = true;
      daftarEl.scrollTo({ top: tujuan, behavior: "smooth" });
      window.setTimeout(() => {
        kunciScrollDaftar = false;
      }, 500);
    }

    function gambarPaginasi(jumlah) {
      const total = Math.ceil(jumlah / UKURAN_SURAT);
      paginasiEl.replaceChildren();
      if (!total) {
        paginasiEl.hidden = true;
        return;
      }
      paginasiEl.hidden = false;
      for (let n = 1; n <= total; n += 1) {
        const tombol = document.createElement("button");
        tombol.type = "button";
        tombol.dataset.halaman = String(n);
        tombol.textContent = String(n);
        tombol.addEventListener("click", () => lompatHalamanDaftar(n));
        paginasiEl.append(tombol);
      }
      tandaiHalamanDaftar();
    }

    function lompatKeAyat(nilai) {
      if (!suratData) {
        return;
      }
      halamanGlobal = null;
      tandaiHalamanGlobal();
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
      if (halamanGlobal && halamanTampil()) {
        const urutan = urutanGlobal();
        const posisi = lembarGlobal + langkah;
        if (posisi < 0 || posisi >= urutan.length) {
          return;
        }
        lembarGlobal = posisi;
        halamanGlobal = urutan[posisi].id;
        tampilkanHalamanGlobal();
        return;
      }
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
    modeXtcBtn.addEventListener("click", () => aturModeViewer("xtc"));
    modeEpubBtn.addEventListener("click", () => aturModeViewer("epub"));
    modeKindleBtn.addEventListener("click", () => aturModeViewer("kindle"));
    document.querySelectorAll("#halaman-global [data-halaman]").forEach((btn) => {
      btn.addEventListener("click", () => bukaHalamanGlobal(btn.dataset.halaman));
    });
    document.getElementById("ekspor-quran").addEventListener("click", () => eksporQuran(document.getElementById("ekspor-quran")));
    document.getElementById("ekspor-quran-kindle").addEventListener("click", () => eksporQuranKindle(document.getElementById("ekspor-quran-kindle")));
    cariEl.addEventListener("input", () => {
      halamanDaftar = 1;
      tampilkanDaftar({ keAtas: true });
    });
    let scrollDijadwal = false;
    daftarEl.addEventListener("scroll", () => {
      if (kunciScrollDaftar || scrollDijadwal) {
        return;
      }
      scrollDijadwal = true;
      window.requestAnimationFrame(() => {
        scrollDijadwal = false;
        tandaiHalamanSaatScroll();
      });
    });

    function tandaiHalamanSaatScroll() {
      const items = daftarEl.querySelectorAll("li");
      if (!items.length) {
        return;
      }
      const batas = daftarEl.getBoundingClientRect().top + 8;
      let indeks = 0;
      items.forEach((li, i) => {
        if (li.getBoundingClientRect().top <= batas) {
          indeks = i;
        }
      });
      const halaman = Math.floor(indeks / UKURAN_SURAT) + 1;
      if (halaman !== halamanDaftar) {
        halamanDaftar = halaman;
        tandaiHalamanDaftar();
      }
    }
    const bahasaEl = document.getElementById("bahasa");
    bahasaEl.addEventListener("change", async () => {
      const ayat = nomorAyatTampil();
      bahasa = bahasaEl.value;
      terapkanAntarmuka();
      if (aktif) {
        await bukaSurat(aktif);
        if (ayat && modeViewer !== "kindle") {
          lompatKeAyat(ayat);
        }
      } else if (halamanGlobal && modeViewer === "kindle") {
        tampilkanKindle();
      } else if (halamanGlobal && halamanTampil()) {
        tampilkanHalamanGlobal();
      } else if (suratData) {
        tampilkanHalaman();
      }
    });
    revalidasiBtn.addEventListener("click", () => revalidasiSalinan());
    revalidasiDialog.addEventListener("cancel", (event) => {
      if (revalidasiBerjalan) {
        event.preventDefault();
      }
    });
    revalidasiTutup.addEventListener("click", () => revalidasiDialog.close());

    function aturProgresRevalidasi(selesai, total, status) {
      const persen = total === 0 ? 0 : Math.round((selesai / total) * 100);
      revalidasiBar.style.width = persen + "%";
      revalidasiTrack.setAttribute("aria-valuenow", String(persen));
      revalidasiTrack.setAttribute("aria-valuetext", status);
      revalidasiStatus.textContent = status;
    }

    async function bacaProgres(response) {
      if (!response.body) {
        throw new Error("Progres tidak tersedia.");
      }
      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let buffer = "";
      let hasil = null;
      const terima = (teks) => {
        const potong = teks.trim();
        if (!potong || potong[0] !== "{") {
          return;
        }
        const event = JSON.parse(potong);
        if (event.code && event.code !== 200) {
          throw new Error(event.message || "Gagal memperbarui salinan.");
        }
        if (event.tahap === "unduh") {
          aturProgresRevalidasi(event.selesai, 115, event.selesai + " / " + event.total);
        } else if (event.tahap === "verifikasi") {
          aturProgresRevalidasi(114, 115, "Verifying local copy");
        } else if (event.tahap === "selesai") {
          hasil = event;
        }
      };
      while (true) {
        const bagian = await reader.read();
        buffer += decoder.decode(bagian.value || new Uint8Array(), { stream: !bagian.done });
        const baris = buffer.split("\n");
        buffer = baris.pop() || "";
        baris.forEach(terima);
        if (bagian.done) {
          if (buffer.trim()) {
            terima(buffer);
          }
          break;
        }
      }
      if (!hasil) {
        throw new Error("Gagal memperbarui salinan.");
      }
      return hasil;
    }

    async function revalidasiSalinan() {
      if (revalidasiBtn.disabled) {
        return;
      }
      revalidasiBtn.disabled = true;
      revalidasiBerjalan = true;
      revalidasiTutup.hidden = true;
      aturProgresRevalidasi(0, 115, "0 / 114");
      revalidasiDialog.showModal();
      try {
        const response = await fetch("revalidasi.php", { method: "POST" });
        if (!response.ok && !response.body) {
          throw new Error("Gagal memperbarui salinan.");
        }
        const body = await bacaProgres(response);
        aturProgresRevalidasi(115, 115, "114 / 114");
        diperbaruiEl.dateTime = body.diperbarui;
        diperbaruiEl.textContent = body.teks;
        pecahanDaftar = null;
        tataCache.clear();
        kunciTata = null;
        halamanCache = null;
        suratGagal.clear();
        surat = await ambil("/surat");
        daftarStatus.hidden = true;
        cekValidasi();
        tampilkanDaftar();
        if (aktif) {
          await bukaSurat(aktif);
        } else if (halamanGlobal && modeViewer === "kindle") {
          tampilkanKindle();
        } else if (halamanGlobal && halamanTampil()) {
          tampilkanHalamanGlobal();
        }
        revalidasiDialog.close();
      } catch (error) {
        revalidasiStatus.textContent = error.message || "Gagal memperbarui salinan.";
        revalidasiTrack.setAttribute("aria-valuetext", revalidasiStatus.textContent);
        revalidasiTutup.hidden = false;
      } finally {
        revalidasiBerjalan = false;
        revalidasiBtn.disabled = false;
      }
    }

    // Pengukuran halaman bergantung pada font; tunggu font lokal siap sebelum surat pertama dibuka.
    const fontSiap = Promise.all([
      "400 16px Lato", "italic 400 16px Lato", "700 16px Lato",
      "300 16px 'Noto Sans Arabic'", "400 16px 'Noto Sans Arabic'", "700 16px 'Noto Sans Arabic'",
      "750 16px 'Noto Kufi Arabic'", "400 16px 'Scheherazade New'",
    ].map((font) => document.fonts.load(font))).catch(() => {});

    Promise.all([ambil("/surat"), ambil("/informasi").catch(() => ({})), fontSiap])
      .then(([data, info]) => {
        sumberLain = info || {};
        surat = data;
        daftarStatus.hidden = true;
        cekValidasi();
        tampilkanDaftar();
        bukaSurat(1);
      })
      .catch((error) => {
        daftarStatus.className = "status error";
        daftarStatus.textContent = error.message || (antarmukaInggris() ? "Failed to load the surah list." : "Gagal memuat daftar surat.");
      });
  </script>
</body>
</html>
