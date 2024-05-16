<?php
require_once 'ReferenceStrategy.php';
class BookStrategy implements ReferenceStrategy {
    public function parse($text) {
        // Define el patrón para detectar referencias de libros
        $pattern = '/(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ª)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)/';
        
        // Busca coincidencias con el patrón en el texto de la referencia
        if (preg_match($pattern, $text, $matches)) {
            // Si hay una coincidencia, retorna un array asociativo con la expresión y el valor capturado
            return array('expression' => 'Book', 'value' => $matches[0]);
        } else {
            // Si no hay coincidencias, retorna un mensaje indicando que no se encontró la referencia de un libro
            return array('expression' => 'No book reference found', 'value' => '');
        }
    }
}
