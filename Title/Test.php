<?php
require_once 'ReferenceStrategyFactory.php';
require_once 'ReferenceParser.php';
require_once 'BookStrategy.php';
require_once 'CharapterStrategy.php';
require_once 'CongressStrategy.php';
require_once 'JournalStrategy.php';

// Supongamos que tienes una referencia bibliográfica
$reference = 'Apellido Autor, N. N. (1994). Título del trabajo. (3ª ed., Vol. 4). Editorial.';

// Utiliza la fábrica de estrategias para crear la estrategia adecuada
//$strategy = ReferenceStrategyFactory::createStrategy($reference);

$author = AuthorExpression::parse($referenceText);


// Crea una instancia de ReferenceParser con la estrategia seleccionada
$referenceParser = new ReferenceParser();
$referenceParser->setStrategy(new BookStrategy());
$result = $referenceParser->parse($reference);

echo "Expresión que coincidió: " . $result['expression'] . "\n";
echo "Valor capturado: " . $result['value'] . "\n";
echo "Titulo: " . $result['value']['book'] . "\n";
echo "Edicion: " . $result['value']['edicion'] . "\n";
echo "Editorial: " . $result['value']['editorial'] . "\n";