<?php
require_once 'Reference.php';

// Supongamos que tienes una referencia bibliográfica
$reference = 'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';

// Crea una instancia de ReferenceParser con la estrategia seleccionada
$referenceParser = new Reference($reference);


$referenceParser->printer();