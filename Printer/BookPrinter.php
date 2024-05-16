<?php
class BookPrinter {
    public static function print($titleBook){
        echo "Titulo: " . $titleBook['book'] . "\n";
        echo "Edicion: " . $titleBook['edicion'] . "\n";
        echo "Editorial: " . $titleBook['editorial'] . "\n";
    }

    public static function getString($titleBook){
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return $titleBook['book'].' ('.$titleBook['edicion'].'). '.$titleBook['editorial'].'.';
    }
}