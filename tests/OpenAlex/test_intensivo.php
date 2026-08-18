<?php

/**
 * Script de Pruebas Intensivas para la Integración con OpenAlex
 * 
 * Lee el archivo de texto plano: tests/OpenAlex/data/referencias.txt
 * Ejecuta el parsing y la consulta en lote a la API de OpenAlex sin modificar código fuente del proyecto.
 * Analiza una por una cada referencia inspeccionando el DOM XML generado por ReferencesManager.
 * 
 * Uso: php tests/OpenAlex/test_intensivo.php
 */

require_once __DIR__ . '/../../ReferencesManager.php';

function color($text, $code) { return "\033[{$code}m{$text}\033[0m"; }

$inputFile = __DIR__ . '/data/referencias.txt';
if (!file_exists($inputFile)) {
    fwrite(STDERR, "Archivo de referencias no encontrado en: {$inputFile}\n");
    exit(1);
}

// Leer cada línea ignorando líneas vacías y comentarios de encabezado
$rawLines = file($inputFile, FILE_IGNORE_NEW_LINES);
$references = [];
foreach ($rawLines as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '//') === 0) {
        continue;
    }
    $references[] = $line;
}

$totalRefs = count($references);
if ($totalRefs === 0) {
    echo color("⚠ El archivo referencias.txt está vacío. Agrega referencias y vuelve a ejecutar.\n", "33");
    exit(0);
}

echo color("=========================================================================\n", "34");
echo color("     SCRIPT DE PRUEBAS INTENSIVAS DE ENRIQUECIMIENTO CON OPENALEX\n", "34");
echo color("=========================================================================\n", "34");
echo "Referencias a evaluar: " . color("{$totalRefs}", "1") . "\n";
echo "Archivo origen: tests/OpenAlex/data/referencias.txt\n\n";
echo "Consultando API de OpenAlex y procesando referencias...\n\n";

// Preparar el documento DOM y el nodo <back>
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$back = $dom->createElement('back');
$dom->appendChild($back);

// Instanciar ReferencesManager para procesar todo el lote
$start = microtime(true);
$manager = new ReferencesManager($dom, $back, $references);
$elapsed = round(microtime(true) - $start, 2);

// Inspeccionar únicamente la estructura DOM pública generada por ReferencesManager
$refNodes = $dom->getElementsByTagName('ref');

$jatsList = $manager->getJatsList();

$enrichedCount = 0;
$failedCount = 0;
$failureReasons = [];
$reportData = [];

foreach ($refNodes as $idx => $refNode) {
    $num = $idx + 1;
    $refId = $refNode->getAttribute('id');
    $mixedCitationNode = $refNode->getElementsByTagName('mixed-citation')->item(0);
    $elementCitationNode = $refNode->getElementsByTagName('element-citation')->item(0);
    
    $fullMixedText = $mixedCitationNode ? $mixedCitationNode->nodeValue : '';
    $origRefText = $references[$idx] ?? $fullMixedText;

    // Obtener metadatos del objeto JATSReference
    $jatsObj = $jatsList[$idx] ?? null;
    $sourceType = $jatsObj ? $jatsObj->getOpenAlexSourceType() : null;
    $enricherClass = $jatsObj ? $jatsObj->getEnricherUsed() : null;
    $isFallback = $jatsObj ? $jatsObj->isFallbackEnricher() : false;

    $enricherLabel = $enricherClass;
    if ($enricherClass && $isFallback) {
        $enricherLabel .= ' (fallback)';
    }

    // Extraer DOI si existe en la cita o en el XML
    $doi = null;
    if (preg_match('~10\.\d{4,9}/[-._;()/:A-Za-z0-9]+~', $origRefText, $m)) {
        $doi = rtrim($m[0], '.');
    }

    // Detectar si ReferencesManager inyectó marcas de error en mixed-citation
    $hasErrorMarker = (strpos($fullMixedText, '--- ERRORS FOUND IN THESE SECTIONS:') !== false);
    $errorString = '';
    if ($hasErrorMarker && preg_match('~--- ERRORS FOUND IN THESE SECTIONS:\s*"([^"]+)"~i', $fullMixedText, $em)) {
        $errorString = trim($em[1]);
    }

    // Una referencia fue enriquecida si NO tiene marcas de error y tiene element-citation
    $isEnriched = (!$hasErrorMarker && $elementCitationNode !== null);

    $category = '';
    $reason = '';

    if ($isEnriched) {
        $enrichedCount++;
        $statusStr = color("ENRIQUECIDA", "32");
        $category = "Enriquecida Exitosamente";
    } else {
        $failedCount++;
        $statusStr = color("NO ENRIQUECIDA", "31");

        if (empty($doi)) {
            $category = "Sin DOI Detectado";
            $reason = "La cita original en texto plano no contiene un DOI válido.";
        } else if (strpos($errorString, 'does not exist in OpenAlex database') !== false) {
            $category = "DOI Inexistente en OpenAlex";
            $reason = "El DOI '{$doi}' no fue hallado en la base de datos de OpenAlex.";
        } else if (strpos($errorString, 'not found in OpenAlex data') !== false) {
            $category = "Mismatch de Autores";
            $reason = "Los autores de la cita no coinciden con los de OpenAlex para el DOI '{$doi}'.";
        } else if (strpos($errorString, 'No printer class found') !== false) {
            $category = "Printer No Disponible";
            $reason = "No existe la clase Printer para el tipo de fuente detectado en OpenAlex.";
        } else if (strpos($errorString, 'Author.') !== false || strpos($errorString, 'Title.') !== false) {
            $category = "Error de Parseo Inicial";
            $reason = "Error al analizar el texto original (falta título o autor).";
        } else {
            $category = "Error en Enriquecimiento";
            $reason = !empty($errorString) ? $errorString : "No se pudo enriquecer la referencia.";
        }

        if (!isset($failureReasons[$category])) {
            $failureReasons[$category] = 0;
        }
        $failureReasons[$category]++;
    }

    $reportData[] = [
        'id' => $num,
        'status' => $isEnriched ? 'ENRIQUECIDA' : 'NO_ENRIQUECIDA',
        'category' => $category,
        'doi' => $doi ?? '',
        'source_type' => $sourceType ?? 'N/A',
        'enricher' => $enricherLabel ?? 'N/A',
        'reason' => $reason,
        'text' => $origRefText
    ];

    echo color("[Cita #{$num}] ", "36") . $statusStr . " | DOI: " . ($doi ?: "Ninguno") . "\n";
    if ($sourceType || $enricherLabel) {
        echo "  Fuente OpenAlex : " . color($sourceType ?? 'Desconocida', "35") . "\n";
        echo "  Enricher Usado  : " . color($enricherLabel ?? 'Ninguno', "33") . "\n";
    }
    echo "  Texto: " . mb_substr($origRefText, 0, 90) . "...\n";
    if (!$isEnriched) {
        echo color("  Motivo de fallo: ", "31") . "{$category} - {$reason}\n";
    }
    echo "\n";
}

// Calcular porcentajes
$successRate = round(($enrichedCount / $totalRefs) * 100, 1);
$failureRate = round(($failedCount / $totalRefs) * 100, 1);

// Guardar XML JATS final
$outputDir = __DIR__ . '/output';
if (!is_dir($outputDir)) {
    @mkdir($outputDir, 0777, true);
}
$xmlFile = $outputDir . '/resultado_jats.xml';
$dom->save($xmlFile);

// Guardar Reporte CSV para exportación a Excel
$csvFile = $outputDir . '/reporte_detalle.csv';
$fp = fopen($csvFile, 'w');
fputcsv($fp, ['#', 'Estado', 'Categoría / Motivo', 'DOI', 'Fuente OpenAlex', 'Enricher Usado', 'Detalle Error', 'Texto Cita Original']);
foreach ($reportData as $row) {
    fputcsv($fp, [$row['id'], $row['status'], $row['category'], $row['doi'], $row['source_type'], $row['enricher'], $row['reason'], $row['text']]);
}
fclose($fp);

// Resumen Estadístico en Consola
echo color("=========================================================================\n", "34");
echo color("                       ESTADÍSTICAS Y MÉTRICAS FINAL\n", "34");
echo color("=========================================================================\n\n", "34");

echo "Tiempo total de procesamiento: {$elapsed} segundos\n";
echo "Total de referencias evaluadas: " . color("{$totalRefs}", "1") . "\n\n";

echo "  • Enriquecidas correctamente: " . color("{$enrichedCount} ({$successRate}%)", "32") . "\n";
echo "  • No enriquecidas / Fallidas: " . color("{$failedCount} ({$failureRate}%)", "31") . "\n\n";

if (!empty($failureReasons)) {
    echo color("--- DESGLOSE DE MOTIVOS DE FALLO ---\n", "33");
    foreach ($failureReasons as $catName => $count) {
        $pct = round(($count / $totalRefs) * 100, 1);
        echo sprintf("  - %-35s : %2d (%4.1f%%)\n", $catName, $count, $pct);
    }
    echo "\n";
}

echo color("=========================================================================\n", "34");
echo color("ARCHIVOS GUARDADOS:\n", "1");
echo "  1. XML JATS resultante : {$xmlFile}\n";
echo "  2. Reporte CSV (Excel)  : {$csvFile}\n";
echo color("=========================================================================\n\n", "34");

