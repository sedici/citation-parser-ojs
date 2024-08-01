<?php
class Parser {

    public static function parse($str){

        //filtrar autores y fecha
        $re = '/^(.*?)\(\d{1}(.*?)\)\.(.*)$/';
        preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
        $str = $matches[3][0];

        $patternChapter = '/(.*?)\.\sEn(.*?)\,\s(.*?)\s\((.*?)\)\.\s(.*?)\./';
        $patternJounal = '/(.*)\.(.*?)\,(.*?)\,(.*?)\./';
        $patternBook = '/(.*)\.\s(\((.*?)\)\.\s)(.*?)\./'; 
        $patternThesis = '/(.*)\s\[(.*?)].(.*)/';

        $patterns = array(
            $patternChapter => 'chapter',
            $patternBook => 'book',
            $patternJounal => 'journal',
            $patternThesis => 'thesis'
        );

        $results = array();
        foreach ($patterns as $pattern => $name) {
            // Realizar una búsqueda global para contar todas las coincidencias
            preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);
            
            // Imprimir coincidencias para depuración
            echo "Pattern: $name\n";
            print_r($matches);
            
            // Guardar el patrón y el número de coincidencias
            $results[$name] = count($matches);
        }
        
        // Encontrar el patrón con más coincidencias
        arsort($results); // Ordenar en orden descendente
        $bestMatch = key($results); // Obtener el nombre del patrón con más coincidencias
        
        // Imprimir el patrón con más coincidencias
        echo "Best Match: $bestMatch\n";
        
        // Obtener las coincidencias correspondientes
        if (isset($patterns[array_search($bestMatch, $patterns)])) {
            preg_match($patterns[array_search($bestMatch, $patterns)], $str, $bestMatches, PREG_SET_ORDER);
            $result = array('expression' => $bestMatch, 'value' => $bestMatches);
        } else {
            $result = array('expression' => $bestMatch, 'value' => 'No matches found');
        }
        
        // Mostrar el resultado
        print_r($result);
    }

}
$thesis = 'Apellido, N. (2000). Título de la tesis [Tesis de doctorado no publicada]. Nombre de la Institución Académica. ';
$str = $thesis;
Parser::parse($str);