<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once dirname(__DIR__) . '/Printer/OpenAlexApi/OpenAlexTypeMapper.php';
require_once dirname(__DIR__) . '/Printer/JournalPrinter.php';
require_once dirname(__DIR__) . '/Printer/BookPrinter.php';
require_once dirname(__DIR__) . '/Printer/ChapterPrinter.php';
require_once dirname(__DIR__) . '/Printer/ConfprocPrinter.php';
require_once dirname(__DIR__) . '/Printer/ThesisPrinter.php';
require_once dirname(__DIR__) . '/Printer/GenericPrinter.php';

echo "=== TESTING OPENALEX TYPE MAPPER ===\n\n";

$testCases = [
    // Caso 1: Artículo de revista estándar
    [
        'name' => 'Artículo de revista estándar (article + journal)',
        'payload' => [
            'type' => 'article',
            'primary_location' => ['source' => ['type' => 'journal']]
        ],
        'expectedJats' => 'journal',
        'expectedPrinter' => 'JournalPrinter',
    ],
    // Caso 2: Libro
    [
        'name' => 'Libro (work.type = book)',
        'payload' => [
            'type' => 'book',
            'primary_location' => ['source' => ['type' => null]]
        ],
        'expectedJats' => 'book',
        'expectedPrinter' => 'BookPrinter',
    ],
    // Caso 3: Plataforma de libros electrónicos (E book platform)
    [
        'name' => 'Plataforma de libros electrónicos (source.type = ebook platform)',
        'payload' => [
            'type' => 'book',
            'primary_location' => ['source' => ['type' => 'ebook platform']]
        ],
        'expectedJats' => 'book',
        'expectedPrinter' => 'BookPrinter',
    ],
    // Caso 4: Capítulo de libro en plataforma electrónica
    [
        'name' => 'Capítulo en ebook platform (work.type = book-chapter, source.type = ebook platform)',
        'payload' => [
            'type' => 'book-chapter',
            'primary_location' => ['source' => ['type' => 'ebook platform']]
        ],
        'expectedJats' => 'chapter',
        'expectedPrinter' => 'ChapterPrinter',
    ],
    // Caso 5: Capítulo de libro general
    [
        'name' => 'Capítulo de libro (work.type = book-chapter)',
        'payload' => [
            'type' => 'book-chapter',
            'primary_location' => ['source' => ['type' => 'book series']]
        ],
        'expectedJats' => 'chapter',
        'expectedPrinter' => 'ChapterPrinter',
    ],
    // Caso 6: Conferencia / Congreso por work.type
    [
        'name' => 'Acta de conferencia (work.type = proceedings-article)',
        'payload' => [
            'type' => 'proceedings-article',
            'primary_location' => ['source' => ['type' => 'conference']]
        ],
        'expectedJats' => 'confproc',
        'expectedPrinter' => 'ConfprocPrinter',
    ],
    // Caso 7: Conferencia por source.type
    [
        'name' => 'Canal de conferencia (source.type = conference)',
        'payload' => [
            'type' => 'other',
            'primary_location' => ['source' => ['type' => 'conference']]
        ],
        'expectedJats' => 'confproc',
        'expectedPrinter' => 'ConfprocPrinter',
    ],
    // Caso 8: Tesis doctoral o de grado
    [
        'name' => 'Disertación / Tesis (work.type = dissertation)',
        'payload' => [
            'type' => 'dissertation',
            'primary_location' => ['source' => ['type' => 'repository']]
        ],
        'expectedJats' => 'thesis',
        'expectedPrinter' => 'ThesisPrinter',
    ],
    // Caso 9: Repositorio (Green OA / Zenodo)
    [
        'name' => 'Artículo en repositorio (source.type = repository)',
        'payload' => [
            'type' => 'article',
            'primary_location' => ['source' => ['type' => 'repository']]
        ],
        'expectedJats' => 'journal',
        'expectedPrinter' => 'JournalPrinter',
    ],
    // Caso 10: Preprint en repositorio
    [
        'name' => 'Preprint en repositorio (work.type = preprint, source.type = repository)',
        'payload' => [
            'type' => 'preprint',
            'primary_location' => ['source' => ['type' => 'repository']]
        ],
        'expectedJats' => 'preprint',
        'expectedPrinter' => 'JournalPrinter',
    ],
    // Caso 11: Reporte
    [
        'name' => 'Informe técnico (work.type = report)',
        'payload' => [
            'type' => 'report',
            'primary_location' => null
        ],
        'expectedJats' => 'report',
        'expectedPrinter' => 'GenericPrinter',
    ],
    // Caso 12: Conjunto de datos (Dataset)
    [
        'name' => 'Dataset (work.type = dataset)',
        'payload' => [
            'type' => 'dataset',
            'primary_location' => ['source' => ['type' => 'repository']]
        ],
        'expectedJats' => 'data',
        'expectedPrinter' => 'GenericPrinter',
    ],
    // Caso 13: Payload vacío / desconocido
    [
        'name' => 'Payload vacío / sin tipo',
        'payload' => [],
        'expectedJats' => 'journal',
        'expectedPrinter' => 'JournalPrinter',
    ],
];

$passed = 0;
$failed = 0;

foreach ($testCases as $case) {
    $resolvedJats = OpenAlexTypeMapper::resolveJatsPublicationType($case['payload']);
    $resolvedPrinter = OpenAlexTypeMapper::resolvePrinterClass($case['payload']);

    $jatsOk = ($resolvedJats === $case['expectedJats']);
    $printerOk = ($resolvedPrinter === $case['expectedPrinter']);

    if ($jatsOk && $printerOk) {
        echo "[PASS] {$case['name']} -> JATS: '{$resolvedJats}', Printer: '{$resolvedPrinter}'\n";
        $passed++;
    } else {
        echo "[FAIL] {$case['name']}\n";
        if (!$jatsOk) {
            echo "       Esperado JATS: '{$case['expectedJats']}', Obtenido: '{$resolvedJats}'\n";
        }
        if (!$printerOk) {
            echo "       Esperado Printer: '{$case['expectedPrinter']}', Obtenido: '{$resolvedPrinter}'\n";
        }
        $failed++;
    }
}

// Test de soporte de enriquecimiento
echo "\n--- Test isEnrichmentSupported ---\n";
$enrichmentCheck1 = OpenAlexTypeMapper::isEnrichmentSupported('JournalPrinter');
$enrichmentCheck2 = OpenAlexTypeMapper::isEnrichmentSupported('BookPrinter');
$enrichmentCheck3 = OpenAlexTypeMapper::isEnrichmentSupported('InexistentPrinter');

if ($enrichmentCheck1 === true && $enrichmentCheck2 === false && $enrichmentCheck3 === false) {
    echo "[PASS] isEnrichmentSupported detecta correctamente 'JournalPrinter' (true), 'BookPrinter' (false) y clases inexistentes (false).\n";
    $passed++;
} else {
    echo "[FAIL] isEnrichmentSupported falló en las verificaciones.\n";
    $failed++;
}

echo "\nResumen: {$passed} superadas, {$failed} fallidas.\n";
if ($failed > 0) {
    exit(1);
}
