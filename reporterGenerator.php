<?php
require_once 'Reference.php';
require_once 'JATSReference.php';
// Crear el documento DOM para generar el XML
$dom = new \DOMDocument('1.0', 'UTF-8');

// Leer el archivo de texto con referencias
$referencias = file('examples/ayana/14758/ayana.14758.original.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Nombre del archivo CSV
$fileCsv = 'informe_referencias_jats.csv';

// Abrir el archivo CSV en modo de escritura
$handleCsv = fopen($fileCsv, 'w');

// Escribir la primera fila con los encabezados
fputcsv($handleCsv, ["reference", "author", "date", "title", "url", "match", "incomplete match"]);

// Procesar cada referencia
foreach ($referencias as $referencia) {
    // Crear el objeto Reference y JATSReference (como en tu código)
    $reference = new Reference(referenceText: $referencia);

    $matchAuthor = !empty($reference->getAuthor()) ? true : false;
    $matchDate = !empty($reference->getDate()) ? true : false;
    $matchTtile = !empty($reference->getTitle()) ? true : false;
    $matchUrl = !empty($reference->getURL()) ? true : false;
    // Agregar la referencia al CSV en la primera columna
    // Puedes dejar las otras columnas vacías o llenarlas según necesites
    $matchComplete = ($matchAuthor && $matchDate  && $matchTtile && $matchUrl) ? true : false; 
    $matchIncomplete = ($matchAuthor || $matchDate || $matchTitle || $matchUrl) ? true : false; 
    
    fputcsv($handleCsv, [$referencia, $matchAuthor, $matchDate, $matchTtile, $matchUrl, $matchComplete, $matchIncomplete]);

    $jats = new JATSReference(reference: $reference, dom: $dom);
    // Generar el XML JATS para la referencia
    $jats->getJatsXML();
}

// Guardar el archivo XML
$dom->save('informe_referencias_jats.xml');

// Cerrar el archivo CSV
fclose($handleCsv);

echo "Informe CSV y XML generados exitosamente.";
?>
