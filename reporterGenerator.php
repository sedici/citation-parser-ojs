<?php
require_once 'Reference.php';
require_once 'JATSReference.php';
// Crear el documento DOM para generar el XML
$dom = new \DOMDocument('1.0', 'UTF-8');

// Leer el archivo de texto con referencias
$referencias = file('examples/ayana/14758/ayana.14758.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Nombre del archivo CSV
$fileCsv = 'informe_referencias_jats.csv';

// Abrir el archivo CSV en modo de escritura
$handleCsv = fopen($fileCsv, 'w');

// Escribir la primera fila con los encabezados
fputcsv($handleCsv, ["reference", "author", "date", "title", "url", "match", "incomplete match", "espected type", "result type"]);

// Procesar cada referencia
foreach ($referencias as $index => $referencia) {
    // Crear el objeto Reference y JATSReference (como en tu código)
    $reference = new Reference(plainTextReference: $referencia);

    $matchAuthor = !empty($reference->getAuthor()) ? true : false;
    $matchDate = !empty($reference->getDate()) ? true : false;
    $matchTitle = !empty($reference->getTitle()) ? true : false;
    $matchUrl = !empty($reference->getURL()) ? true : false;
    $matchType = $reference->getType();
    
    // Agregar la referencia al CSV en la primera columna
    // Puedes dejar las otras columnas vacías o llenarlas según necesites
    $matchComplete = ($matchAuthor && $matchDate && $matchTitle) ? true : false; 
    $matchIncomplete = ($matchAuthor || $matchDate || $matchTitle) ? true : false; 
    
    // Agregar el tipo esperado y el tipo resultante (aquí puedes definir cómo obtener estos tipos)
    $expectedType = 'congress'; // Definir o calcular el tipo esperado
    $resultType = $matchType; // Este es el tipo obtenido

    fputcsv($handleCsv, [$referencia, $matchAuthor, $matchDate, $matchTitle, $matchUrl, $matchComplete, $matchIncomplete, $expectedType, $resultType]);

    $jats = new JATSReference(reference: $reference, dom: $dom, id: $index);
    // Generar el XML JATS para la referencia
    $jats->getJatsXML();
}

// Guardar el archivo XML
$dom->save('informe_referencias_jats.xml');

// Nombre del archivo CSV
$fileCsv = 'informe_referencias_jats.csv';

// Contadores para `matchComplete`, `author`, `date`, `title`, `url`, y total de filas
$matchCompleteCount = 0;
$authorCount = 0;
$dateCount = 0;
$titleCount = 0;
$urlCount = 0;
$totalRows = 0;
$totalColumns = 0;

// Arreglo para almacenar los números de las filas con `matchComplete = 1`
$matchCompleteRows = [];
$correctTypeCount = 0; // Contador para tipos correctos

// Abrir el archivo CSV en modo de lectura
if (($handleCsv = fopen($fileCsv, 'r')) !== false) {
    // Leer la primera fila para contar el número de columnas
    $header = fgetcsv($handleCsv);
    $totalColumns = count($header);

    // Leer cada fila en el CSV
    while (($data = fgetcsv($handleCsv)) !== false) {
        $totalRows++; // Incrementa el contador de filas
        
        // Contar coincidencias en cada columna
        if (isset($data[1]) && $data[1] == 1) $authorCount++;
        if (isset($data[2]) && $data[2] == 1) $dateCount++;
        if (isset($data[3]) && $data[3] == 1) $titleCount++;
        if (isset($data[4]) && $data[4] == 1) $urlCount++;

        // Suponiendo que `matchComplete` esté en la sexta columna (índice 5)
        if (isset($data[5]) && $data[5] == 1) {
            $matchCompleteCount++;
            $matchCompleteRows[] = [
                'row' => $totalRows, 
                'expectedType' => $data[7], // `espected type` (índice 7)
                'resultType' => $data[8]    // `result type` (índice 8)
            ];
            // Compara el tipo esperado y el tipo resultante
            if ($data[7] === $data[8]) {
                $correctTypeCount++; // Incrementa si son iguales
            }
        }
    }
    fclose($handleCsv);
}

// Calcular los porcentajes
$matchCompletePercentage = ($matchCompleteCount / $totalRows) * 100;
$authorPercentage = ($authorCount / $totalRows) * 100;
$datePercentage = ($dateCount / $totalRows) * 100;
$titlePercentage = ($titleCount / $totalRows) * 100;
$urlPercentage = ($urlCount / $totalRows) * 100;
$precisionPercentage = ($matchCompleteCount > 0) ? ($correctTypeCount / $matchCompleteCount) * 100 : 0; // Porcentaje de precisión

// Imprimir resultados en formato de tabla en la terminal
echo "------------------------------------------\n";
echo "| Campo           | Porcentaje de Coincidencias |\n";
echo "------------------------------------------\n";
echo "| Author          | " . str_pad(number_format($authorPercentage, 2) . "%", 20) . "|\n";
echo "| Date            | " . str_pad(number_format($datePercentage, 2) . "%", 20) . "|\n";
echo "| Title           | " . str_pad(number_format($titlePercentage, 2) . "%", 20) . "|\n";
echo "| URL             | " . str_pad(number_format($urlPercentage, 2) . "%", 20) . "|\n";
echo "------------------------------------------\n";
echo "| Coincidencia Completa (matchComplete)  |\n";
echo "------------------------------------------\n";
echo "| " . str_pad("Total", 15) . "| " . str_pad(number_format($matchCompletePercentage, 2) . "%", 20) . "|\n";
echo "------------------------------------------\n";

// Imprimir las filas que tienen `matchComplete = 1`
echo "\nFilas con matchComplete: \n";
foreach ($matchCompleteRows as $row) {
    echo "Fila " . $row['row'] . " - Esperado: " . $row['expectedType'] . ", Resultado: " . $row['resultType'] . "\n";
}

// Imprimir el porcentaje de precisión en formato de tabla
echo "\n------------------------------------------\n";
echo "| Porcentaje de Precisión                 |\n";
echo "------------------------------------------\n";
echo "| " . str_pad(number_format($precisionPercentage, 2) . "%", 20) . "|\n";
echo "------------------------------------------\n";
?>
