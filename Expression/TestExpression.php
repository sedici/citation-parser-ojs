<?php
require_once 'AuthorExpression.php';
require_once 'TitleExpression.php';

$reference = 'Apellido Autor, N. N. (1994). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
$author = AuthorExpression::parse($reference);
print_r($author);

$reference = 'Castañeda Naranjo, L. A. y Palacios Neri, J. (2015). Nanotecnología: fuente de nuevos paradigmas. Mundo Nano. Revista Interdisciplinaria en Nanociencias y Nanotecnología, 7(12), 45–49.';
$title = TitleExpression::parse($reference);
print_r($title);

$reference = 'Castañeda Naranjo, L. A. y Palacios Neri, J. (2015). Nanotecnología fuente de nuevos paradigmas Mundo Nano. Revista Interdisciplinaria en Nanociencias y Nanotecnología, 7(12), 45–49.';
$title = TitleExpression::parse($reference);
print_r($title);