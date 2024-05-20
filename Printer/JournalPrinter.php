<?php
include_once 'ReferencePrinter.php';
class JournalPrinter extends ReferencePrinter {

    public static function getReferenceString($reference): string{
        return $reference['journal'].$reference['revista'].', '.$reference['nedicio'].'('.$reference['volumen'].'), '.$reference['paginas'].'.';
    }
}