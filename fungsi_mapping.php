<?php
// fungsi_mapping.php
// Fungsi untuk memetakan teks input ke kode gejala G1..G12

/**
 * @param string $input_text  Teks input dari user (dipisah koma)
 * @param array  $gejala      Array gejala resmi
 * @param int    $threshold   Ambang batas kemiripan (persen)
 *
 * @return array [$gejala_aktif, $input_bersih]
 */
function mappingGejala($input_text, $gejala, $threshold = 70)
{
    $input_text = strtolower($input_text);
    $tokens = array_filter(array_map('trim', explode(',', $input_text)));

    $gejala_aktif = [];
    $input_bersih = [];

    foreach ($tokens as $token) {
        if ($token === '') {
            continue;
        }

        $input_bersih[] = $token;

        $bestKode = null;
        $bestScore = 0;

        foreach ($gejala as $kode => $nama) {
            similar_text($token, strtolower($nama), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestKode = $kode;
            }
        }

        // jika kemiripan cukup tinggi, anggap cocok
        if ($bestKode !== null && $bestScore >= $threshold) {
            $gejala_aktif[] = $bestKode;
        }
    }

    // hilangkan duplikasi
    $gejala_aktif = array_values(array_unique($gejala_aktif));

    return [$gejala_aktif, $input_bersih];
}
