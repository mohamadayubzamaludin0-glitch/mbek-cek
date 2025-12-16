<?php
// fungsi_bayes.php
// Fungsi untuk menghitung Naive Bayes berdasarkan gejala aktif

/**
 * @param array $gejalaAktif   Daftar kode gejala yang terdeteksi (misal: ['G1','G3'])
 * @param array $penyakit      Data penyakit (prior + prob_gejala)
 *
 * @return array $posterior    Probabilitas P(H|E) yang sudah diurutkan dari terbesar
 */
function hitungBayes($gejalaAktif, $penyakit)
{
    $numerator = [];
    $sum = 0.0;

    foreach ($penyakit as $kode_p => $data_p) {
        $prior = $data_p['prior'];
        $likelihood = 1.0;

        // Kalikan semua P(Gk|H) untuk gejala yang aktif & relevan
        foreach ($gejalaAktif as $gk) {
            if (isset($data_p['prob_gejala'][$gk])) {
                $likelihood *= $data_p['prob_gejala'][$gk];
            }
        }

        $numerator[$kode_p] = $likelihood * $prior;
        $sum += $numerator[$kode_p];
    }

    $posterior = [];

    if ($sum > 0) {
        foreach ($penyakit as $kode_p => $data_p) {
            $posterior[$kode_p] = [
                'nama' => $data_p['nama'],
                'nilai' => $numerator[$kode_p] / $sum,
            ];
        }
    } else {
        // Kalau sum = 0, semua probabilitas dianggap 0
        foreach ($penyakit as $kode_p => $data_p) {
            $posterior[$kode_p] = [
                'nama' => $data_p['nama'],
                'nilai' => 0,
            ];
        }
    }

    // urutkan dari yang terbesar ke yang terkecil
    uasort($posterior, function ($a, $b) {
        if ($a['nilai'] == $b['nilai'])
            return 0;
        return ($a['nilai'] > $b['nilai']) ? -1 : 1;
    });

    return $posterior;
}
