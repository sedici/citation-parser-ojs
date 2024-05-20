<?php
include_once 'ReferencePrinter.php';
class AuthorPrinter extends ReferencePrinter {

    public static function getReferenceString($reference): string{
        // Inicializamos una variable para almacenar la cadena resultante
        $result = '';

        // Contamos la cantidad de autores
        $authorCount = count($reference);

        // Iteramos sobre el array de autores
        foreach ($reference as $index => $author) {
            $result .= $author;
            
            // Si no es el último autor, agregamos una coma y un espacio
            if ($index < $authorCount - 1) {
                $result .= ', ';
            }
        }

        // Imprimimos el resultado
        return $result;
    }
}