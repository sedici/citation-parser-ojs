<?php
class Parser {

    public static function parse($str){

        //filtrar autores y fecha
        $re = '/^(.*?)\(\d{1}(.*?)\)\.(.*)$/';
        preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);
        $str = $matches[3][0];

        $patternChapter = '/(.*?)\.\sEn(.*?)\)\,\s(.*?)\s\((.*?)\)\.\s(.*?)\./';
        $patternJounal = '/(.*)\.(.*?)\,(.*?)\,(.*?)\./';
        $patternBook = '/(.*)\.\s(\((.*?)\)\.\s)(.*?)\./'; 
        //$patternBook = '/(.*?(?!\(.*\)\.))\.\s(\((.*?)\)\.\s)?(.*?)\./';
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
        $cant = $results[$bestMatch];

        // Imprimir el patrón con más coincidencias
        echo "Best Match: $bestMatch\n";
        // Obtener las coincidencias correspondientes
        
        if($cant == 0){
            return array('expression' => 'No match found', 'value' => '');
        }


        $bestPattern = array_search($bestMatch, array_values($patterns));
        $re = array_keys($patterns)[$bestPattern];
        preg_match_all($re, $str, $bestMatches, PREG_SET_ORDER);
        
        $matches = $bestMatches[0];
        $value = $matches;

        if(strcmp($bestMatch,'chapter') == 0){

            /*
            $autores = array();
            foreach ($matches[2] as $key => $authorGroup) {
                preg_match_all('/([^,]+)(?:,|$)/', $authorGroup, $authorMatches);
                $autores[$key] = $authorMatches[1];
            }
            */

            $value = array(
                'title' => $matches[1],
                'autores' => $matches[2],
                'editorial' => $matches[3],
                'edicion' => $matches[4],

            );

        } else if(strcmp($bestMatch,'journal') == 0){
            
            $value = array( 
                'title' => $matches[1],
                'revista' => $matches[2],
                'nedicio' => $matches[3],
                'volumen' => $matches[3],
                'paginas' =>  $matches[4],
                'fpage' =>  $matches[4],
                'lpage' =>  $matches[4],
            );

        } else if(strcmp($bestMatch,'book') == 0) { 
            
            $value = array( 
                'title' => $matches[1],
                'edicion' => $matches[2],
                'nedicio' => $matches[2],
                'volumen' => $matches[2],
                'editorial' =>  $matches[3],
            );

        } else if(strcmp($bestMatch,'thesis') == 0){            

            $value = array( 
                'title' => $matches[1],
                'comment' => $matches[2],
            );
        }

        // Mostrar el resultado
        $result = array('expression' => $bestMatch, 'value' => $value);
        return $result;
    }

    
}
/*
$text = file('examples/ayana/14758/ayana.14758.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
// Recorrer cada cadena en $text y parsear
foreach ($text as $str) {
    $results[] = Parser::parse($str);
}

// Contar cuántas veces 'expression' => 'No match found' aparece en los resultados
$noMatchCount = 0;

foreach ($results as $result) {
    if (isset($result['expression']) && $result['expression'] === 'No match found') {
        $noMatchCount++;
    }
}
    */
