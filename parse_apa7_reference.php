<?php

//----------------------------//
//       AUTOR/EDITOR         //
//----------------------------//  

//------------  ELEMENTS OF AUTHORS ------------//


$apellido = "(?P<apellido>\p{L}+(\s\p{L}+)*)";
$nombre = "(?P<nombres>(\p{Lu}\.\s?)+)";

$author = "(?P<author>".$apellido.", ".$nombre.")";
//$author = '/^(?P<apellidos>([A-Za-zÀ-ÿáéíóú]+\s?)+),\s(?P<nombres>([A-Za-z]\.\s)+)/';

$authors = '/'.$author.'((?:,\s|y\s))?/';
//$manyauthors = '/(?P<author>(?P<apellido>\p{L}+(\s\p{L}+)*), (?P<nombres>(\p{Lu}\.\s?)+))((?:,\s|y\s))?/';

$lastauthor = '/'.'y\s'.$author.'/';
// "y Garcias M."

function get_authors($text) {
    $apellido = "(?P<apellido>\p{L}+(\s\p{L}+)*)";
    $nombre = "(?P<nombres>(\p{Lu}\.\s?)+)";
    $author = "(?P<author>".$apellido.", ".$nombre.")";
    $authors = '/'.$author.'((?:,\s|y\s))?/';

    preg_match_all($authors, $text, $matches, PREG_SET_ORDER);

    $authors_array = array();

    foreach ($matches as $match) {
        $apellido = $match['apellido'];
        $nombres = $match['nombres'];
        $author = $apellido . ", " . $nombres;
        $authors_array[] = $author;
    }

    return $authors_array;
}

//------------    INSTITUTIONS   ------------//






//--------------------------------------------//
//            DATE OF PUBLICATION             //
//--------------------------------------------//

//Recupera el valor que va dentro de los parentesis que representan la fecha. No chequea que este mal o bien
$daterecolector = '/\.\s\((?P<fecha>(?>(\d.[^)]*|s\.f\.[a-z]?)))/';

$year = '/(?P<fecha>\d{4})/';
// 2023 
$periodyear = '/(?P<fecha>\d{4}(\/\d{4})?)/';
// 2023/2024

$espesificdate = '\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})';
$datecomplete = '/(?P<fecha>'.$espesificdate.'/'; 
// 15 de abril de 2024

// --- PERIODDAY --- //
/* Fechas que solo son para congresos */

    $periodday = '/(?P<fecha>\d{2}-'.$espesificdate.'/';
    // 15-17 de abril de 2024

    $periodmonth = '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}-'.$espesificdate.'/';
    // 31 de marzo-2 de abril de 2024

// -------------------//

// Fecha de recuperacion
$recoverydate = '/Recuperado el\s(?P<fecha>\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})\sde\s(?P<url>https?:\/\/[^\s]+)/';
// ejemplo: Recuperado el 15 de marzo de 2023 de https://google.com


//----------------------------//
//            TITLE           //
//----------------------------//

/*
La información de título se refiere al título de la obra que se cita. Podemos dividir los títulos en dos grandes categorías:

    títulos de trabajos completos (libros, informes, disertaciones, tesis, videos de youtube, películas, álbumes, podcasts, redes sociales y sitios web);
    títulos de trabajos que son parte de algo (artículos periódicos, capítulos de libros editados, episodios de TV y canciones, por ejemplo).

*/

//------------ELEMENTS OF BIBLIOGRAPHIC REFERENCES ------------//

// Capturar la edicion para LIBRO o capitulo de libro (para el capitulo faltaria las paginas)
$edicion = '/(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)/';
// 1ᵃ ed., Vol. 5 | 1ᵃ ed., Vol. III | 1ᵃ ed.

// Captura la edicion para CAPITULO DE LIBRO donde se incluye el numero de pagina
$edicioncomplete = '/(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)/';
// 1ᵃ ed., Vol. 4, pp. 1-1000

$url = '/(?P<url>https?:\/\/[^\s]+)/';
// https://google.com | http://wikipedia.com



//--------------------------------------------------------------//



//------------BOOK------------//

// complete regex expression from book
$book = '/^(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)/';
//falta la URL
 
// concatenated regex expression from book
$edicion = '(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)';
$concedicion = '(\s\('.$edicion.'\)\.)?';

$url = '(?P<url>https?:\/\/[^\s]+)';
$opcionalurl = '(\s'. $url .')?/';

$bookcomplete = '/^(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s]+\.)'.$concedicion.'\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)'.$opcionalurl;

//-----------CHAPTER-------------//

// complete regex expression from charapter
$chapter = '/^(?P<chapter>[A-Z][A-Za-zÀ-ÿ\s]+\.)\sEn.\s,(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?))\)\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/';
//falta autores


//-----------JOURNAL-------------//
// NO ACEPTA PUNTOS EL TITULO. SOLO ACEPTA PUNTOS EN LA REVISTA.


// No reconocer numero de articulo, solo pp.
$journal = '/(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s\:]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>\d{1,3})\((?P<volumen>\d{1,3})\)\,\s(?P<paginas>(\d{1,4}-\d{1,4}))\./';
// Estructura "Titulo articulo. Revista,10(20),10-20. url"
//  

//-----------PAGINAS WEB-------------//




//----------------------------//
//           TESTS            //
//----------------------------//

/*
$texto = "Thompson, V., Striemer, C., Reikoff, R., Gunter, R. y Campbell, J. (2003).";

preg_match_all($manyauthors, $texto, $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    echo "Apellido: " . $match['apellido'] . PHP_EOL;
    echo "Nombres: " . $match['nombres'] . PHP_EOL;
    echo "---------------------" . PHP_EOL;
}
*/ 



$texto = "Thompson Thompson, V. F. H., Striemer, C., Reikoff, R., Gunter, R. y Campbell, J. L. J. (2003).";

$authors_array = get_authors($texto);

print_r($authors_array);


?>
