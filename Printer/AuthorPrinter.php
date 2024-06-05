<?php
include_once 'GenericPrinter.php';
class AuthorPrinter extends GenericPrinter {

    public function toPlainText(): string{
        // Inicializamos una variable para almacenar la cadena resultante
        $result = '';


        // Iteramos sobre el array de autores
        foreach ($this->reference as $index => $author) {
            $result .= $author['nombre'];

        }

        // Imprimimos el resultado
        return $result;
    }

    public function createXMLElements(): array {
        $elements = [];
        return $elements;
    }
}