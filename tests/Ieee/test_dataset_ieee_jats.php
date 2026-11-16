<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

/**
 * Script de Pruebas Masivas para el Parser IEEE y Generación de JATS XML mediante Printers
 *
 * Procesa el 100% de las referencias del dataset grande (1.376 referencias de TEyET, UIS, UNAL, Guía IEEE).
 * Ejecuta el parseo IEEE (IeeeReferenceStrategy) y genera el árbol JATS XML (<ref-list>) usando los Printers.
 * Guarda el XML JATS resultante en tests/Ieee/output/jats_ieee_printers_output.xml y un reporte CSV.
 *
 * Uso: php tests/Ieee/test_dataset_ieee_jats.php
 */

require_once __DIR__ . '/../../Reference.php';
require_once __DIR__ . '/../../JATSReference.php';
require_once __DIR__ . '/../../Printer/OpenAlexApi/OpenAlexApi.php';

function color(string $text, string $code): string {
    return "\033[{$code}m{$text}\033[0m";
}

echo color("=========================================================================\n", "34");
echo color("     PRUEBA MASIVA DATASET IEEE 1376 -> GENERACIÓN JATS XML (PRINTERS)\n", "34");
echo color("=========================================================================\n", "34");

// 1. Localizar el dataset
$candidatePaths = [
    __DIR__ . '/data/dataset_ieee_1000.json',
    __DIR__ . '/../../../../dataset_builder/dataset/dataset_ieee_1000.json',
    '/home/tomas/Escritorio/pps/dataset_builder/dataset/dataset_ieee_1000.json'
];

$datasetPath = null;
foreach ($candidatePaths as $path) {
    if (file_exists($path)) {
        $datasetPath = realpath($path);
        break;
    }
}

if (!$datasetPath) {
    fwrite(STDERR, color("❌ Error: No se encontró el dataset en ninguna de las rutas esperadas.\n", "31"));
    exit(1);
}

echo "Dataset origen: " . color($datasetPath, "36") . "\n";
$data = json_decode(file_get_contents($datasetPath), true);
$totalRefs = count($data);
echo "Total de referencias cargadas: " . color((string)$totalRefs, "1;32") . "\n\n";

// Deshabilitar llamadas remotas de red para evaluar la salida pura de los Printers locales
OpenAlexAPI::$enableNetwork = false;

// Preparar directorios de salida
$outputDir = __DIR__ . '/output';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$xmlOutputFile = $outputDir . '/jats_ieee_printers_output.xml';
$csvOutputFile = $outputDir . '/reporte_ieee_printers.csv';

// 2. Preparar el DOM Document JATS XML con namespace estándar JATS (Texture/XLink)
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$article = $dom->createElement('article');
$article->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
$article->setAttribute('xmlns:mml', 'http://www.w3.org/1998/Math/MathML');
$dom->appendChild($article);
$back = $dom->createElement('back');
$article->appendChild($back);
$refList = $dom->createElement('ref-list');
$back->appendChild($refList);

// Estadísticas de ejecución
$start = microtime(true);
$typeStats = [];
$successCount = 0;
$errorCount = 0;
$errorSections = [];
$csvRows = [];
$csvRows[] = ["ID", "Tipo Detectado", "Autores", "Tiene Titulo", "Tiene Fuente", "Anio", "DOI", "URL", "Resultado XML", "Errores"];

echo "Procesando referencias y construyendo elementos JATS...\n";

foreach ($data as $index => $item) {
    $rawRef = $item['raw_reference'] ?? $item['referencia'] ?? '';
    $refId = $item['id'] ?? ($index + 1);

    // 1. Instanciar Reference con estrategia IEEE
    $refObj = new Reference($rawRef, 'ieee');
    $pubType = $refObj->getType();
    $typeStats[$pubType] = ($typeStats[$pubType] ?? 0) + 1;

    // 2. Crear nodo JATSReference y generar elementos mediante Printers
    $jats = new JATSReference($dom, $refList, $refObj, (int)$refId);
    $jats->createXMLElemetns();

    // 3. Inspeccionar el nodo generado
    $currentRefNode = $refList->childNodes->item($index);
    $elementCitation = $currentRefNode ? $currentRefNode->getElementsByTagName('element-citation')->item(0) : null;
    $mixedCitation = $currentRefNode ? $currentRefNode->getElementsByTagName('mixed-citation')->item(0) : null;

    $authorsData = $refObj->getAuthor()['authors'] ?? [];
    $titleData = $refObj->getTitle();
    $dateData = $refObj->getDate();
    $urlData = $refObj->getURL();

    $hasTitle = !empty($titleData['title']) || !empty($titleData['titulo']);
    $hasSource = !empty($titleData['source']) || !empty($titleData['revista']) || !empty($titleData['book']);
    $year = $dateData['year'] ?? '';
    $doi = $urlData['doi'] ?? '';
    $url = $urlData['url'] ?? '';

    if ($elementCitation !== null) {
        $successCount++;
        $status = "OK";
        $errorsFound = "";
    } else {
        $errorCount++;
        $status = "ERROR";
        $errorsFound = "Desconocido";
        if ($mixedCitation && preg_match('/ERRORS FOUND IN THESE SECTIONS: "([^"]+)"/', $mixedCitation->textContent, $m)) {
            $errorsFound = trim($m[1]);
            $errorSections[$errorsFound] = ($errorSections[$errorsFound] ?? 0) + 1;
        }
    }

    $csvRows[] = [
        $refId,
        $pubType,
        count($authorsData),
        $hasTitle ? 'SI' : 'NO',
        $hasSource ? 'SI' : 'NO',
        $year,
        $doi,
        $url,
        $status,
        $errorsFound
    ];
}

$elapsed = microtime(true) - $start;
$avgMs = round(($elapsed / $totalRefs) * 1000, 2);

// 3. Guardar el archivo JATS XML resultante
$xmlContent = $dom->saveXML();
file_put_contents($xmlOutputFile, $xmlContent);
$xmlSizeBytes = filesize($xmlOutputFile);
$xmlSizeKb = round($xmlSizeBytes / 1024, 1);

// 4. Guardar el reporte CSV
$fp = fopen($csvOutputFile, 'w');
foreach ($csvRows as $row) {
    fputcsv($fp, $row);
}
fclose($fp);

// 5. Validar que el XML resultante sea bien formado
libxml_use_internal_errors(true);
$sxml = simplexml_load_string($xmlContent);
$xmlValid = ($sxml !== false);
libxml_clear_errors();

// 6. Imprimir informe final en consola
echo "\n" . color("=========================================================================\n", "32");
echo color("                           RESUMEN DE RESULTADOS\n", "32");
echo color("=========================================================================\n", "32");
echo "Tiempo total:            " . color(round($elapsed, 3) . " segundos", "1") . " ({$avgMs} ms por referencia)\n";
echo "Referencias evaluadas:   " . color((string)$totalRefs, "1") . "\n";
$successPct = round(($successCount / $totalRefs) * 100, 2);
echo "Con <element-citation>:  " . color("{$successCount} ({$successPct}%)", "1;32") . " [ÉXITO]\n";
echo "Con fallos en secciones: " . color("{$errorCount}", $errorCount > 0 ? "33" : "32") . "\n";

echo "\n" . color("Distribución por Tipo de Publicación (JATS publication-type):\n", "1;36");
foreach ($typeStats as $type => $count) {
    $pct = round(($count / $totalRefs) * 100, 1);
    printf("  %-15s : %5d (%5.1f%%)\n", $type, $count, $pct);
}

if (!empty($errorSections)) {
    echo "\n" . color("Desglose de Secciones Faltantes:\n", "33");
    foreach ($errorSections as $sec => $count) {
        echo "  - {$sec}: {$count}\n";
    }
}

echo "\n" . color("Archivos Generados:\n", "1;35");
echo "  📄 JATS XML:   " . color($xmlOutputFile, "36") . " ({$xmlSizeKb} KB)\n";
echo "  📊 Reporte CSV:" . color($csvOutputFile, "36") . "\n";
echo "  XML Bien Formado: " . ($xmlValid ? color("SÍ (Válido)", "1;32") : color("NO (Inválido)", "1;31")) . "\n";

echo "\n" . color("=========================================================================\n", "32");

if (!$xmlValid) {
    exit(1);
}
exit(0);
