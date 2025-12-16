<?php
// index.php
// Halaman utama: form input & hasil diagnosa

require_once 'gejala.php';
require_once 'penyakit.php';
require_once 'fungsi_mapping.php';
require_once 'fungsi_bayes.php';

$has_result = false;
$gejala_aktif = [];
$gejala_input_bersih = [];
$hasil_posterior = [];
$diagnosa_tertinggi = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_text = isset($_POST['teks_gejala']) ? $_POST['teks_gejala'] : '';

    // 1. mapping input text ke gejala resmi (G1..G12)
    list($gejala_aktif, $gejala_input_bersih) = mappingGejala($input_text, $gejala, 70);

    // 2. jika ada gejala yang dikenali, hitung Naive Bayes
    if (!empty($gejala_aktif)) {
        $hasil_posterior = hitungBayes($gejala_aktif, $penyakit);

        // Ambil penyakit dengan probabilitas terbesar
        if (!empty($hasil_posterior)) {
            $firstRow = reset($hasil_posterior); // value pertama
            $firstKey = key($hasil_posterior);   // key pertama

            $diagnosa_tertinggi = [
                'kode' => $firstKey,
                'nama' => $firstRow['nama'],
                'nilai' => $firstRow['nilai'],
            ];
        }
    }

    $has_result = true;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sistem Pakar Penyakit Kambing</title>
    <style>
        /* ===============================
   RESET & GLOBAL
================================ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #fdf7ee;
            color: #2c2c2c;
            line-height: 1.7;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===============================
   NAVIGATION
================================ */
        .main-nav {
            background-color: #fdf7ee;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-inner {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            column-gap: 20px;
        }

        .main-nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 28px;
            padding: 16px 0;
            margin: 0;
            align-items: center;
            justify-self: center;
        }

        .main-nav a {
            text-decoration: none;
            font-size: 14px;
            color: #444;
            font-weight: 400;

        }

        .main-nav a.active {
            border-bottom: 1px solid #444;
            color: #222;
        }

        /* ===============================
   HERO / HEADER
================================ */
        .main-header {
            background-color: #fdf7ee;
            text-align: center;
        }

        .main-header h1 {
            font-family: "Georgia", serif;
            font-size: 44px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .main-header p {
            font-size: 16px;
            color: #666;
        }

        /* ===============================
   HERO IMAGE
================================ */
        .header-image-container {
            margin-top: 60px;
            max-width: 760px;
            margin-left: auto;
            margin-right: auto;
        }

        .mbek {
            width: 80%;
            max-width: 720px;
            height: auto;
        }

        .image-caption {
            margin-top: 24px;
            font-size: 14px;
            color: #777;
            font-style: italic;
        }

        /* ===============================
   SECTION
================================ */
        .page-section {
            margin-top: 110px;
            scroll-margin-top: 120px;
        }

        .section-heading h2 {
            font-family: "Georgia", serif;
            font-size: 26px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .section-heading p {
            font-size: 14px;
            color: #666;
        }

        /* ===============================
   LAYOUT
================================ */
        .content-wrapper {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
            margin-top: 40px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            margin-top: 40px;
        }

        /* ===============================
   CARD
================================ */
        .card {
            background-color: #ffffff;
            padding: 32px;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.04);
        }

        .card h2 {
            font-family: "Georgia", serif;
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 16px;
        }

        /* ===============================
   FORM
================================ */
        .form-group textarea {
            width: 100%;
            min-height: 120px;
            padding: 16px;
            font-size: 14px;
            border: 1px solid #ddd;
            background-color: #fafafa;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            margin-top: 12px;
            background-color: #2c2c2c;
            color: #fff;
            padding: 12px 28px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #000;
        }

        /* ===============================
   RESULT & LIST
================================ */
        .result-section h3 {
            font-family: "Georgia", serif;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .symptom-list {
            list-style: none;
            margin-top: 10px;
        }

        .symptom-list li {
            background-color: #f4f1ea;
            padding: 10px 14px;
            margin-bottom: 6px;
            font-size: 14px;
        }

        /* ===============================
   HIGHLIGHT BOX
================================ */
        .highlight-box {
            background-color: #f4efe6;
            border-left: 4px solid #c8a96a;
            padding: 18px;
            margin-top: 20px;
        }

        .highlight-box h4 {
            font-family: "Georgia", serif;
            font-size: 16px;
            margin-bottom: 6px;
        }

        /* ===============================
   TABLE
================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        th {
            background-color: #fafafa;
            font-weight: 500;
        }

        /* ===============================
   STEP LIST
================================ */
        .step-list {
            padding-left: 18px;
            font-size: 14px;
            color: #555;
        }

        .step-list li {
            margin-bottom: 10px;
        }

        /* ===============================
   RESPONSIVE
================================ */
        @media (max-width: 768px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }

            .main-header h1 {
                font-size: 32px;
            }
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-title {
            font-family: "Georgia", serif;
            font-size: 18px;
            font-weight: 600;
            color: #2c2c2c;
            margin: 0;
            text-align: left;
            justify-self: start;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 24px;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <nav class="main-nav">
        <div class="container nav-inner">
            <h1 class="nav-title">Mbek-Cek</h1>
            <ul>
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#diagnosa">Diagnosa</a></li>
                <li><a href="#informasi">Informasi</a></li>
                <li><a href="#tentang">Tentang</a></li>
            </ul>
        </div>
    </nav>
    <header class="main-header" id="beranda">
        <div class="header-image-container">
            <img src="Mbek-Cek.png" alt="Kambing" class="mbek">
        
        </div>
    </header>



    <main class="container">
        <section id="diagnosa" class="page-section">
            <div class="section-heading">
                <h2>Diagnosa</h2>
                <p>Klik menu untuk melompat ke bagian ini dan masukkan gejala kambing yang sedang diperiksa.</p>
            </div>

            <div class="content-wrapper">
                <!-- KOLOM KIRI: FORM + HASIL -->
                <div>
                    <!-- FORM INPUT -->
                    <section class="card">
                        <h2>Diagnosa Penyakit Berdasarkan Gejala</h2>
                        <p>Masukkan gejala-gejala yang terlihat pada kambing Anda.</p>

                        <div class="form-group">
                            <form method="post" action="#hasil">
                                <textarea name="teks_gejala" rows="5" placeholder="Sesuaikan dengan gejala disamping"><?php
                                if (!empty($_POST['teks_gejala'])) {
                                    echo htmlspecialchars($_POST['teks_gejala']);
                                }
                                ?></textarea>
                                <button type="submit" class="btn">Proses Diagnosa</button>
                            </form>
                        </div>
                    </section>

                    <!-- HASIL DIAGNOSA -->
                    <?php if ($has_result): ?>
                        <section id="hasil" class="card result-section">
                            <h3>Hasil Diagnosa</h3>

                            <?php if (!empty($gejala_input_bersih)): ?>
                                <p><strong>Gejala yang Anda input:</strong></p>
                                <ul class="symptom-list">
                                    <?php foreach ($gejala_input_bersih as $t): ?>
                                        <li><?php echo htmlspecialchars($t); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if (!empty($gejala_aktif)): ?>
                                <p><strong>Gejala yang dikenali sistem:</strong></p>
                                <ul class="symptom-list">
                                    <?php foreach ($gejala_aktif as $kode_g): ?>
                                        <li><?php echo htmlspecialchars($kode_g . ' - ' . $gejala[$kode_g]); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>Tidak ada gejala yang dikenali. Silakan periksa kembali input Anda.</p>
                            <?php endif; ?>

                            <?php if ($diagnosa_tertinggi && !empty($hasil_posterior) && !empty($gejala_aktif)): ?>
                                <div class="highlight-box">
                                    <h4>Penyakit yang paling mungkin:</h4>
                                    <p><strong><?php echo htmlspecialchars($diagnosa_tertinggi['nama']); ?>
                                            (<?php echo htmlspecialchars($diagnosa_tertinggi['kode']); ?>)</strong></p>
                                    <p>Probabilitas:
                                        <strong><?php echo number_format($diagnosa_tertinggi['nilai'] * 100, 2); ?>%</strong>
                                    </p>
                                </div>

                                <h4>Detail Probabilitas Semua Penyakit</h4>
                                <table>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Penyakit</th>
                                        <th>Probabilitas</th>
                                    </tr>
                                    <?php foreach ($hasil_posterior as $kode_p => $row): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($kode_p); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><?php echo number_format($row['nilai'] * 100, 4); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php elseif (!empty($gejala_aktif)): ?>
                                <p>Gejala dikenali, tetapi hasil perhitungan 0. Cek kembali data di file penyakit.php.</p>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>
                </div>

                <!-- KOLOM KANAN: DAFTAR GEJALA -->
                <div>
                    <section class="card">
                        <h2>Daftar Gejala yang Didukung</h2>
                        <p>Berikut adalah daftar gejala yang dapat dikenali oleh sistem:</p>
                        <ul class="symptom-list">
                            <?php foreach ($gejala as $kode => $nama): ?>
                                <li><?php echo htmlspecialchars($kode . ' - ' . $nama); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
            </div>
        </section>

        <section id="informasi" class="page-section">
            <div class="section-heading">
                <h2>Informasi</h2>
                <p>Lihat penyakit yang dicakup dan alur singkat cara kerja sistem pakar.</p>
            </div>
            <div class="info-grid">
                <div class="card">
                    <h2>Daftar Penyakit</h2>
                    <p>Sistem saat ini melakukan diagnosa untuk penyakit berikut:</p>
                    <ul class="symptom-list">
                        <?php foreach ($penyakit as $kode => $data): ?>
                            <li><?php echo htmlspecialchars($kode . ' - ' . $data['nama']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card">
                    <h2>Cara Menggunakan</h2>
                    <p>Langkah cepat menggunakan menu Diagnosa:</p>
                    <ol class="step-list">
                        <li>Klik menu <strong>Diagnosa</strong> atau gulir ke bagian formulir.</li>
                        <li>Tulis gejala kambing secara deskriptif, pisahkan dengan koma.</li>
                        <li>Klik <strong>Proses Diagnosa</strong> untuk melihat gejala yang dikenali.</li>
                        <li>Perhatikan penyakit dengan probabilitas tertinggi pada hasil.</li>
                    </ol>
                </div>
            </div>
        </section>

        <section id="tentang" class="page-section">
            <div class="card">
                <h2>Tentang Sistem</h2>
                <p>Aplikasi ini menggunakan metode Naive Bayes untuk menghitung kemungkinan penyakit kambing berdasarkan
                    gejala yang Anda tulis bebas.</p>
                <p>Setiap klik pada menu akan menggulir halus ke bagiannya, sehingga memudahkan navigasi dan membaca
                    informasi penting tanpa berpindah halaman.</p>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.main-nav a');
            const sections = Array.from(navLinks)
                .map(link => document.querySelector(link.getAttribute('href')))
                .filter(Boolean);

            const setActiveLink = () => {
                const scrollPos = window.scrollY + 140;
                let currentSection = sections[0];

                sections.forEach(section => {
                    if (section.offsetTop <= scrollPos) {
                        currentSection = section;
                    }
                });

                navLinks.forEach(link => {
                    const target = document.querySelector(link.getAttribute('href'));
                    link.classList.toggle('active', target === currentSection);
                });
            };

            setActiveLink();
            window.addEventListener('scroll', setActiveLink);

            // Scroll otomatis ke hasil setelah submit (jika ada)
            <?php if ($has_result): ?>
            const hasilSection = document.getElementById('hasil');
            if (hasilSection) {
                hasilSection.scrollIntoView({ behavior: 'smooth'});
            }
            <?php endif; ?>
        });
    </script>
</body>

</html>
