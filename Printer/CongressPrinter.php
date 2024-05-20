<?php
include_once 'ReferencePrinter.php';
class CongressPrinter extends ReferencePrinter{

    public static function getReferenceString($reference): string{
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return $reference['book'].' ('.$reference['edicion'].'). '.$reference['editorial'].'.';
    }
}