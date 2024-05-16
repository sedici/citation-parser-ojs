<?php
require_once __DIR__ . '/TitleStrategys/BookStrategy.php';

class ReferenceStrategyFactory {
    public static function createStrategy($text) {
        // Expresiones regulares para detectar los diferentes tipos de referencias
        $bookPattern = '//'; // Patrón para libros
        $chapterPattern = '//'; // Patrón para capítulos
        $journalPattern = '//'; // Patrón para revistas

        

        // Determinar el tipo de referencia basado en el texto
        return new BookStrategy();
    
    }
}
