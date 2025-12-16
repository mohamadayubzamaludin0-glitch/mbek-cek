<?php
// penyakit.php
// Data penyakit (hipotesis), prior, dan P(G|H)

$penyakit = [
    'P1' => [
        'nama' => 'Cacingan',
        'prior' => 0.30,
        'prob_gejala' => [
            'G1' => 0.8,
            'G2' => 0.7,
            'G3' => 0.6,
            'G5' => 0.8,
            'G12' => 0.7,
        ],
    ],
    'P2' => [
        'nama' => 'Diare Infeksius',
        'prior' => 0.20,
        'prob_gejala' => [
            'G3' => 0.9,
            'G4' => 0.8,
            'G12' => 0.7,
        ],
    ],
    'P3' => [
        'nama' => 'Pneumonia',
        'prior' => 0.15,
        'prob_gejala' => [
            'G6' => 0.9,
            'G7' => 0.8,
            'G8' => 0.75,
        ],
    ],
    'P4' => [
        'nama' => 'Mastitis',
        'prior' => 0.10,
        'prob_gejala' => [
            'G9' => 0.85,
            'G1' => 0.7,
            'G8' => 0.8,
        ],
    ],
    'P5' => [
        'nama' => 'Kudis / Scabies',
        'prior' => 0.15,
        'prob_gejala' => [
            'G10' => 0.9,
            'G11' => 0.9,
            'G2' => 0.7,
        ],
    ],
    'P6' => [
        'nama' => 'Enterotoxemia',
        'prior' => 0.10,
        'prob_gejala' => [
            'G1' => 0.7,
            'G3' => 0.8,
            'G8' => 0.8,
            'G12' => 0.9,
        ],
    ],
];
