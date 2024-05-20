<?php
include_once 'ReferencePrinter.php';
class ChapterPrinter extends ReferencePrinter{
    //IMCOMPLETO LA IMPRESION, FALTA LOS AUTORES.

    public static function getReferenceString($reference): string{
        return $reference['chapter'].' ('.$reference['book'].'). '.$reference['edicion'].'.'.$reference['editorial'].'.';
    }
}