<?php
require_once 'Expression/Parser.php';

$text = file('examples/ayana/14758/ayana.14758.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$results = [];
$noMatchPositions = [];
$noMatchStrings = [];

// Recorrer cada cadena en $text y parsear
foreach ($text as $index => $str) {
    $result = Parser::parse($str);
    $results[] = $result;

    // Verificar si el resultado es 'No match found' y almacenar la posición y la cadena original
    if (isset($result['expression']) && $result['expression'] === 'No match found') {
        $noMatchPositions[] = $index;
        $noMatchStrings[] = $str;
    }
}

// Contar cuántas veces 'expression' => 'No match found' aparece en los resultados
$noMatchCount = count($noMatchPositions);

// Imprimir el conteo de 'No match found'
echo "Número de 'No match found': " . $noMatchCount . "\n";

// Imprimir las posiciones de 'No match found' en orden inverso
echo "Posiciones de 'No match found': " . implode(", ", $noMatchPositions) . "\n";

// Imprimir las cadenas originales que no fueron emparejadas como texto plano separado por un espacio
echo "Cadenas originales que no fueron emparejadas:\n\n" . implode("\n\n", $noMatchStrings) . "\n";