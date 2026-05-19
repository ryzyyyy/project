<?php
require_once '../../config/koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$row = null;
if ($id) {
  $q = mysqli_query($koneksi, "SELECT d.*, sm.nomor_surat, sm.perihal, sm.pengirim, sm.tanggal_surat, sm.lampiran, sm.pengolah, sm.nomor_urut FROM disposisi d LEFT JOIN surat_masuk sm ON d.surat_masuk_id = sm.id WHERE d.id = '$id' LIMIT 1");
  $row = mysqli_fetch_assoc($q);
}
function fmtDate($d) {
  if (!$d || $d == '0000-00-00') return '-';
  $bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  $dt = strtotime($d);
  return date('j', $dt).' '.$bulan[date('n',$dt)-1].' '.date('Y',$dt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<style>
  @media print {
    .print-hide { display: none !important; }
  }
</style>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Template Surat Masuk & Lembar Disposisi</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --ink: #1a1a2e;
    --paper: #faf8f3;
    --border: #2c2c4a;
    --accent: #c8a951;
    --light-border: #aaa;
    --bg: #e8e4d8;
    --stamp: #000000;
  }

  body {
    background: var(--bg);
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 13px;
    color: var(--ink);
    min-height: 100vh;
    padding: 20px;
  }

  /* ── Controls ── */
  .controls {
    max-width: 800px;
    margin: 0 auto 20px;
    background: var(--ink);
    border-radius: 8px;
    padding: 16px 20px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
  }
  .controls label {
    color: #ccc;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
  }
  .controls select {
    background: #fff;
    border: none;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 13px;
    flex: 1;
    min-width: 200px;
    cursor: pointer;
  }
  .controls button {
    background: var(--accent);
    color: var(--ink);
    border: none;
    border-radius: 4px;
    padding: 7px 18px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: opacity .2s;
  }
  .controls button:hover { opacity: .85; }
  .print-btn {
    background: #4a7c59 !important;
    color: #fff !important;
    margin-left: auto;
  }

  /* ── Page wrapper ── */
  .page {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 0;
  }

  /* ── Card (each section of the form) ── */
  .card {
    background: var(--paper);
    border: 2px solid var(--border);
    position: relative;
    page-break-inside: avoid;
  }
  .card + .card { border-top: none; }

  /* ── Section: Surat Masuk Header ── */
  .sm-header {
    text-align: center;
    padding: 10px 12px 6px;
    border-bottom: 1px solid var(--border);
  }
  .sm-header .instansi {
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .04em;
    line-height: 1.4;
  }
  .sm-header .dinas {
    font-family: Tahoma, Geneva, sans-serif;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .03em;
  }

  /* ── Grid rows ── */
  .row {
    display: grid;
    border-bottom: 1px solid var(--border);
  }
  .row:last-child { border-bottom: none; }

  .cell {
    padding: 5px 10px;
    border-right: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 4px;
    min-height: 28px;
  }
  .cell:last-child { border-right: none; }

  .cell-label {
    font-weight: 600;
    white-space: nowrap;
    min-width: 70px;
  }
  .cell-colon { color: #666; margin-right: 2px; }
  .cell-value {
    flex: 1;
    color: var(--stamp);
    font-weight: 600;
    font-size: 13px;
  }
  .cell-value.empty { color: #bbb; font-weight: 400; font-style: italic; font-size: 12px; }

  /* Tall cell for multi-line */
  .cell-tall { align-items: flex-start; padding-top: 8px; min-height: 44px; }

  /* ── Disposisi header ── */
  .disp-header {
    text-align: center;
    padding: 12px;
    border-bottom: 1px solid var(--border);
    background: #f0ece2;
  }
  .disp-header .title {
    font-family: Tahoma, Geneva, sans-serif;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: .05em;
  }
  .disp-header .subtitle {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .04em;
  }

  /* ── Disposition checkboxes ── */
  .disp-table {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-top: 1px solid var(--border);
  }
  .disp-left { border-right: 1px solid var(--border); }
  .disp-section-title {
    text-align: center;
    font-weight: 700;
    padding: 6px;
    background: #f0ece2;
    border-bottom: 1px solid var(--border);
    letter-spacing: .05em;
    font-size: 12px;
    grid-column: 1/-1;
  }
  .disp-check-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 10px;
    border-bottom: 1px solid #ddd;
  }
  .disp-check-row:last-child { border-bottom: none; }
  .disp-check-row input[type=checkbox] { width: 14px; height: 14px; cursor: pointer; accent-color: var(--stamp); }
  .disp-check-row label { cursor: pointer; font-size: 12.5px; }

  /* catatan area */
  .catatan-area {
    padding: 8px 12px;
    min-height: 60px;
    border-top: 1px solid var(--border);
  }
  .catatan-area .lbl { font-size: 11px; color: #888; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .05em; }
  .catatan-area .val { color: var(--stamp); font-weight: 600; }

  /* tanda tangan area */
  .ttd-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 70px;
  }
  .ttd-cell {
    padding: 8px 12px;
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .ttd-cell:last-child { border-right: none; }
  .ttd-cell .lbl { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: .05em; }
  .ttd-cell .line { border-bottom: 1px solid var(--border); margin-top: 40px; }

  /* ── Cut line ── */
  .cut-line {
    border: none;
    border-top: 2px dashed #999;
    margin: 16px 0;
    position: relative;
  }
  .cut-line::before {
    content: '✂';
    position: absolute;
    left: -20px;
    top: -12px;
    font-size: 18px;
    color: #999;
  }

  /* ── Status badge ── */
  .badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
  }
  .badge-biasa { background: #dde8ff; color: #1a3a8f; }
  .badge-penting { background: #fff0cc; color: #7a5000; }
  .badge-rahasia { background: #ffddd9; color: #8b1a1a; }

  /* ── Print ── */
  @media print {
    body { background: white; padding: 0; font-size: 11px; }
    .controls { display: none; }
    .page { max-width: 100%; }
    .cut-line { margin: 8px 0; }
    .card { border: 1.5px solid #000; }
  }

  /* ── Watermark ── */
  .watermark {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%,-50%);
    font-size: 60px;
    font-weight: 900;
    color: rgba(0,0,0,.04);
    font-family: Tahoma, Geneva, sans-serif;
    letter-spacing: .1em;
    pointer-events: none;
    user-select: none;
    white-space: nowrap;
  }
</style>
</head>
<body>


<div style="max-width:800px;margin:0 auto 18px;">
  <div class="print-hide" style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
    <button onclick="goBack()" style="background:#d9534f;color:#fff;border:none;border-radius:4px;padding:8px 22px;font-weight:700;font-size:14px;cursor:pointer;">← Kembali</button>
    <button onclick="window.print()" style="background:#4a7c59;color:#fff;border:none;border-radius:4px;padding:8px 22px;font-weight:700;font-size:14px;cursor:pointer;">🖨 Cetak</button>
  </div>
</div>
<script>
function goBack() {
  if (window.history.length > 1) window.history.back();
  else window.location.href = 'index.php';
}
</script>

<div class="page" id="formPage">

  <!-- ══════════════════════════════════
       BAGIAN 1: SURAT MASUK
  ══════════════════════════════════ -->
  <div class="card" id="cardSuratMasuk">
    <div class="sm-header">
      <div class="instansi" style="font-family: Tahoma, Geneva, sans-serif; font-size: 14px; font-weight: 700; letter-spacing: .04em; line-height: 1.4;">PEMERINTAH KABUPATEN KUDUS</div>
      <div class="dinas" style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; font-weight: 700; letter-spacing: .03em;">DINAS PENDIDIKAN, KEPEMUDAAN DAN OLAHRAGA</div>
    </div>
    <div class="row" style="grid-template-columns: 2fr 2fr 2fr;">
      <?php if ($row && $row['surat_masuk_id'] && $row['surat_masuk_id'] !== '0'): ?>
      <div class="cell">
        <span class="cell-label">Index</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo str_pad($row['index'],3,'0',STR_PAD_LEFT); ?></span>
      </div>
      <?php endif; ?>
      <div class="cell">
        <span class="cell-label">Kode</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo $row ? explode('/',$row['nomor_surat'])[0] : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; padding-top:4px;">
        <div style="display:flex; gap:4px; align-items:center; width:100%;">
          <span class="cell-label">Nomor</span>
          <span class="cell-colon">:</span>
          <span class="cell-value"><?php echo $row ? ($row['nomor_urut'] ? $row['nomor_urut'] : str_pad($row['surat_masuk_id'],3,'0',STR_PAD_LEFT)) : '-'; ?></span>
        </div>
        <div style="display:flex; gap:4px; align-items:center; width:100%; margin-top:2px;">
          <span class="cell-label" style="min-width:70px;">Urut</span>
          <span class="cell-colon"></span>
          <span class="cell-value"></span>
        </div>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 1fr;">
      <div class="cell cell-tall">
        <span class="cell-label">Isi Ringkas</span>
        <span class="cell-colon">:</span>
        <span class="cell-value" style="white-space: pre-wrap;"><?php echo $row ? $row['perihal'] : '-'; ?></span>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 1fr;">
      <div class="cell">
        <span class="cell-label">Dari</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo $row ? $row['pengirim'] : '-'; ?></span>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 2fr 3fr 2fr;">
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:2px;">
        <span style="font-size:11px; color:#888;">Tanggal Surat :</span>
        <span class="cell-value"><?php echo $row ? fmtDate($row['tanggal_surat']) : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:2px;">
        <span style="font-size:11px; color:#888;">Nomor Surat :</span>
        <span class="cell-value"><?php echo $row ? $row['nomor_surat'] : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:2px;">
        <span style="font-size:11px; color:#888;">Lampiran :</span>
        <span class="cell-value"><?php echo $row ? ($row['lampiran'] ?: '-') : '-'; ?></span>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 2fr 2fr 2fr;">
      <div class="cell">
        <span class="cell-label">Pengolah</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo $row ? ($row['pengolah'] ?: '-') : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:2px;">
        <span style="font-size:11px; color:#888;">Tanggal Diteruskan :</span>
        <span class="cell-value"><?php echo $row ? fmtDate($row['tanggal_disposisi']) : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start;">
        <span style="font-size:11px; color:#888;">Tanda Tangan :</span>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 1fr;">
      <div class="cell cell-tall">
        <span class="cell-label">Catatan</span>
        <span class="cell-colon">:</span>
        <span class="cell-value" style="white-space: pre-wrap;"><?php echo $row ? ($row['catatan'] ?: '-') : '-'; ?></span>
      </div>
    </div>
  </div><!-- /card surat masuk -->

  <!-- ══════════════════════════════════
       BAGIAN 2: LEMBAR DISPOSISI
  ══════════════════════════════════ -->
  <div class="card" style="margin-top: 24px; border-top: 2px solid var(--border);">

    <!-- Header -->
    <div class="disp-header">
      <div class="title">LEMBAR DISPOSISI</div>
      <div class="subtitle">DINAS PENDIDIKAN, KEPEMUDAAN DAN OLAHRAGA</div>
    </div>

    <!-- Surat Masuk Tanggal -->
    <div class="row" style="grid-template-columns: 1fr;">
      <div class="cell" style="min-height:26px;">
        <span class="cell-label" style="min-width:160px;">Surat Masuk Tanggal</span>
        <span class="cell-colon">:</span>
        <span class="cell-value" id="disp-tanggal">&nbsp;</span>
      </div>
    </div>

    <!-- Nomor -->
    <div class="row" style="grid-template-columns: 1fr;">
      <div class="cell" style="min-height:26px;">
        <span class="cell-label" style="min-width:160px;">Nomor</span>
        <span class="cell-colon">:</span>
        <span class="cell-value" id="disp-nomor">&nbsp;</span>
      </div>
    </div>

    <!-- Diteruskan kepada Yth -->
    <div class="row" style="grid-template-columns: 1fr;">
      <div class="cell" style="min-height:26px;">
        <span style="font-weight:600;">Diteruskan kepada Yth :</span>
      </div>
    </div>

    <!-- ── BLOK UTAMA: 2 kolom besar (kiri checklist, kanan kosong) ── -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; border-top: 1px solid var(--border);">

      <!-- KOLOM KIRI -->
      <div style="border-right: 1px solid var(--border);">

        <!-- Kepala Dinas — area tinggi seperti di foto -->
        <div style="display:flex; align-items:flex-start; gap:8px; padding:8px 10px; min-height:90px; border-bottom:1px solid var(--border);">
          <input type="checkbox" id="chk-kepala-dinas" style="margin-top:2px; width:14px; height:14px; flex-shrink:0; accent-color:var(--stamp);">
          <label for="chk-kepala-dinas" style="font-size:13px; cursor:pointer;">Kepala Dinas</label>
        </div>

        <!-- Judul DISPOSISI (span kedua kolom sebenarnya tapi kita buat di kiri saja sesuai foto) -->
        <div style="text-align:center; font-weight:700; padding:5px 8px; background:#f0ece2; border-bottom:1px solid var(--border); font-size:12.5px; letter-spacing:.06em;">
          DISPOSISI
        </div>

        <!-- Checklist disposisi -->
        <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border-bottom:1px solid #ddd; min-height:28px;">
          <input type="checkbox" id="chk-sekretaris" style="width:14px; height:14px; flex-shrink:0; accent-color:var(--stamp);">
          <label for="chk-sekretaris" style="font-size:12.5px; cursor:pointer;">Sekretaris</label>
        </div>
        <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border-bottom:1px solid #ddd; min-height:28px;">
          <input type="checkbox" id="chk-dikdas" style="width:14px; height:14px; flex-shrink:0; accent-color:var(--stamp);">
          <label for="chk-dikdas" style="font-size:12.5px; cursor:pointer;">Kepala Bidang Pendidikan Dasar</label>
        </div>
        <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border-bottom:1px solid #ddd; min-height:28px;">
          <input type="checkbox" id="chk-paud" style="width:14px; height:14px; flex-shrink:0; accent-color:var(--stamp);">
          <label for="chk-paud" style="font-size:12.5px; cursor:pointer;">Kepala Bidang PAUD dan Dikmas</label>
        </div>
        <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; border-bottom:1px solid #ddd; min-height:28px;">
          <input type="checkbox" id="chk-olahraga" style="width:14px; height:14px; flex-shrink:0; accent-color:var(--stamp);">
          <label for="chk-olahraga" style="font-size:12.5px; cursor:pointer;">Kepala Bidang Olahraga</label>
        </div>
        <div style="display:flex; align-items:center; gap:8px; padding:6px 10px; min-height:28px;">
          <input type="checkbox" id="chk-kepemudaan" style="width:14px; height:14px; flex-shrink:0; accent-color:var(--stamp);">
          <label for="chk-kepemudaan" style="font-size:12.5px; cursor:pointer;">Kepala Bidang Kepemudaan</label>
        </div>

      </div><!-- /kolom kiri -->

      <!-- KOLOM KANAN — area kosong untuk catatan/instruksi tangan -->
      <div style="min-height:250px; padding:8px 12px; display:flex; flex-direction:column; gap:6px;">
        <!-- area kosong seperti di foto — hanya tampilkan isi disposisi dari DB jika ada -->
        <div id="disp-isi" style="color:var(--stamp); font-weight:600; font-size:12px; white-space:pre-wrap; flex:1;">&nbsp;</div>
      </div>

    </div><!-- /blok utama 2 kolom -->

    <!-- Area kosong besar di bawah (seperti di foto) -->
    <div style="min-height:100px; border-top:1px solid var(--border); padding:8px 12px;">
      <div id="disp-catatan" style="color:var(--stamp); font-size:12px; white-space:pre-wrap;">&nbsp;</div>
    </div>

    <!-- Garis bawah penutup -->
    <div style="border-top:1px solid var(--border); min-height:8px;"></div>

  </div><!-- /card disposisi -->

  <!-- Cut line -->
  <hr class="cut-line">

  <!-- ══════════════════════════════════
       BAGIAN 3: SALINAN (copy slip) — smaller version
  ══════════════════════════════════ -->
  <div class="card" style="font-size:13px;">
    <div class="watermark" style="font-size:180px; color:rgba(0,0,0,.07); font-family:Tahoma,Geneva,sans-serif; letter-spacing:.2em; font-weight:bold; padding-left:40px; margin-top:40px;">ARSIP</div>
    <div class="sm-header" style="padding: 10px 12px 6px; border-bottom: 1px solid var(--border); text-align: center;">
      <div class="instansi" style="font-family: Tahoma, Geneva, sans-serif; font-size: 14px; font-weight: 700; letter-spacing: .04em; line-height: 1.4;">PEMERINTAH KABUPATEN KUDUS</div>
      <div class="dinas" style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; font-weight: 700; letter-spacing: .03em;">DINAS PENDIDIKAN, KEPEMUDAAN DAN OLAHRAGA</div>
    </div>
    <div class="row" style="grid-template-columns: 2fr 2fr 2fr;">
      <?php if ($row && $row['surat_masuk_id'] && $row['surat_masuk_id'] !== '0'): ?>
      <div class="cell" style="min-height:24px;">
        <span class="cell-label">Index</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo str_pad($row['index'],3,'0',STR_PAD_LEFT); ?></span>
      </div>
      <?php endif; ?>
      <div class="cell" style="min-height:24px;">
        <span class="cell-label">Kode</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo $row ? explode('/',$row['nomor_surat'])[0] : '-'; ?></span>
      </div>
      <div class="cell" style="min-height:24px; flex-direction:column; align-items:flex-start;">
        <div style="display:flex; gap:4px; width:100%;">
          <span class="cell-label">Nomor</span><span class="cell-colon">:</span>
          <span class="cell-value"><?php echo $row ? ($row['nomor_urut'] ? $row['nomor_urut'] : str_pad($row['surat_masuk_id'],3,'0',STR_PAD_LEFT)) : '-'; ?></span>
        </div>
        <div style="display:flex; gap:4px; width:100%;">
          <span class="cell-label" style="min-width:70px;">Urut</span><span class="cell-colon"></span>
          <span class="cell-value"></span>
        </div>
      </div>
    </div>
    <div class="row" style="grid-template-columns:1fr;">
      <div class="cell" style="min-height:44px; align-items:flex-start; padding-top:6px; flex-direction:column; gap:0;">
        <div style="display:flex; gap:4px; width:100%; margin-bottom:4px;">
          <span class="cell-label">Isi Ringkas</span>
          <span class="cell-colon">:</span>
          <span class="cell-value" style="white-space: pre-wrap;"><?php echo $row ? $row['perihal'] : '-'; ?></span>
        </div>
      </div>
    </div>
    <div class="row" style="grid-template-columns:1fr;">
      <div class="cell" style="min-height:20px;">
        <span class="cell-label">Dari</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo $row ? $row['pengirim'] : '-'; ?></span>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 2fr 3fr 2fr;">
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:1px; min-height:36px;">
        <span style="font-size:10px; color:#888;">Tanggal Surat :</span>
        <span class="cell-value"><?php echo $row ? fmtDate($row['tanggal_surat']) : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:1px; min-height:36px;">
        <span style="font-size:10px; color:#888;">Nomor Surat :</span>
        <span class="cell-value"><?php echo $row ? $row['nomor_surat'] : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:1px; min-height:36px;">
        <span style="font-size:10px; color:#888;">Lampiran :</span>
        <span class="cell-value"><?php echo $row ? ($row['lampiran'] ?: '-') : '-'; ?></span>
      </div>
    </div>
    <div class="row" style="grid-template-columns: 2fr 2fr 2fr;">
      <div class="cell" style="min-height:24px;">
        <span class="cell-label">Pengolah</span>
        <span class="cell-colon">:</span>
        <span class="cell-value"><?php echo $row ? ($row['pengolah'] ?: '-') : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start; gap:1px; min-height:24px;">
        <span style="font-size:10px; color:#888;">Tanggal Diteruskan :</span>
        <span class="cell-value"><?php echo $row ? fmtDate($row['tanggal_disposisi']) : '-'; ?></span>
      </div>
      <div class="cell" style="flex-direction:column; align-items:flex-start;">
        <span style="font-size:11px; color:#888;">Tanda Tangan :</span>
      </div>
    </div>
    <div class="row" style="grid-template-columns:1fr;">
      <div class="cell" style="min-height:52px; align-items:flex-start; padding-top:6px; flex-direction:column; gap:0;">
        <div style="display:flex; gap:4px; width:100%; margin-bottom:6px;">
          <span class="cell-label">Catatan</span>
          <span class="cell-colon">:</span>
          <span class="cell-value" style="white-space: pre-wrap;"><?php echo $row ? ($row['catatan'] ?: '-') : '-'; ?></span>
        </div>
        <!-- Ruled lines -->
    </div>

  </div><!-- /copy card -->

</div><!-- /page -->

<script>
<!-- JS Dihapus, data diisi dari PHP -->
</body>
<script>
window.addEventListener('DOMContentLoaded', function() {
  // Jika ada surat, pilih surat pertama dan isi otomatis
  if (sel && sel.options.length > 1) {
    sel.selectedIndex = 1;
    loadSurat();
  }
  window.print();
});
</script>
</html>