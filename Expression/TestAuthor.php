<?php
require_once 'AuthorExpression.php';

$reference = 'Apellido Autor, N. N. (1994). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
$author = AuthorExpression::parse($reference);
print_r($author);