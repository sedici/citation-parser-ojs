<?php
require_once 'Reference.php';

// Supongamos que tienes una referencia bibliográfica
$book = 'Herrera Caceres, C. y Rosillo Penia, M. (2019). Confort y eficiencia energética en el diseño de edificaciones. Universidad del Valle.';
$journal = 'Castakeda Naranjo, L. A. y Palacios Neri, J. (2015). Nanotecnología: fuente de nuevos paradigmas. Mundo Nano. Revista Interdisciplinaria en Nanociencias y Nanotecnología, 7(12), 45–49.';
$charapter = 'Apellido, A. y Apellido, B. (2019). Título del capítulo. En N. Apellido (Ed.), Título del libro (pp. 19-21). Editorial.';


// Crea una instancia de ReferenceParser con la estrategia seleccionada
$bookReference = new Reference($book);
$bookReference->print();

echo "\n";
echo "\n";

$jounralReference = new Reference($journal);
$jounralReference->print();


echo "\n";
echo "\n";

$charapterReference = new Reference($charapter);
$charapterReference->print();