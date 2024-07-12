<?php

//----------------------------//
//           AUTHOR           //
//----------------------------//  

//------------  ELEMENTS OF AUTHORS ------------//

//$apellidocamelcase = "(?P<apellido>\p{Lu}\p{L}+(\s\p{Lu}\p{L}+)*)" 
$apellido = "(?P<apellido>\p{L}+(\s\p{L}+)*)";
$nombre = "(?P<nombres>(\p{Lu}\.\s?)+)";

$role = "(?P<role>(\((Ed.|Coord.|Comp.)\)))?";  //Los roles deberian ser individuales 
$roles = "(?P<roles>(\((Eds.|Coords.|Comps.)\)))?";   

$author = "(?P<author>".$apellido.", ".$nombre." ".$role.")"; //Corregir los espacios
$authors = '/'.$author.$roles.'((?:,\s|y\s))?/';

//$authors-all = '/('.$author.'((?:,\s|y\s))?)+'.$roles.'/';

//preg_match_all 
function get_authors($text,$regex) {

    preg_match_all($regex, $text, $matches, PREG_SET_ORDER);

    $authors_array = array();
    foreach ($matches as $match) {
        $authors_array[] = $match['author'];
    }

    return $authors_array;
}


//------------    INSTITUTIONS   ------------//
/* falta definir la posibilidad de recibir una institusion */

//--------------------------------------------//
//            DATE OF PUBLICATION             //
//--------------------------------------------//

//Recupera el valor que va dentro de los parentesis que representan la fecha. No chequea si el valor ingresado es correcto
/*$daterecolector = '/\.\s\((?P<fecha>(?>(\d.[^)]*|s\.f\.[a-z]?)))/';*/

$year = '/(?P<fecha>\d{4})/';// 2023 
$periodyear = '/(?P<fecha>\d{4}(\/\d{4})?)/'; // 2023/2024
$espesificdate = '\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})';
$datecomplete = '/(?P<fecha>'.$espesificdate.'/';  // 15 de abril de 2024

//------------ PERIODDATE ------------//
/* Fechas que solo son para congresos */

$periodday = '/(?P<fecha>\d{2}-'.$espesificdate.'/';// 15-17 de abril de 2024
$periodmonth = '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}-'.$espesificdate.'/';// 31 de marzo-2 de abril de 2024

//------------ RECOVERYDATE ------------//
/* Fechas que solo son para pagina web */

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

$title = '/(?P<title>[A-Z0-9][0-9A-Za-zÀ-ÿ\s\:\,\;\-]+\.)/'; 

//------------ELEMENTS OF BIBLIOGRAPHIC REFERENCES ------------//

// Capturar la edicion para LIBRO o capitulo de libro (para el capitulo faltaria las paginas)
$edicion = '/(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)/';
// 1ᵃ ed., Vol. 5 | 1ᵃ ed., Vol. III | 1ᵃ ed.

// Captura la edicion para CAPITULO DE LIBRO donde se incluye el numero de pagina
$edicioncomplete = '/(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)/';
// 1ᵃ ed., Vol. 4, pp. 1-1000

//------------ URL ------------//

$url = '(?P<url>https?:\/\/[^\s]+)';
$opcionalurl = '(\s'. $url .')?/';

$url = '/(?P<url>https?:\/\/[^\s]+)/'; 
$doi = '/(?P<doi>https?:\/\/doi.org\/[^\s]+)$/';
// https://google.com | http://wikipedia.com

//--------------------------------------------------------------//




//------------BOOK------------//

// complete regex expression from book
$book = '/^(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)/';
//falta la URL
 
// concatenated regex expression from book
$edicion = '(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)';
$edicion_opcional = '(\s\('.$edicion.'\)\.)?';


$bookcomplete = '/^(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s]+\.)'.$edicion_opcional.'\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)'.$opcionalurl;

//-----------CHAPTER-------------//

/* annotations: 
 -> NO ACEPTA ACENTOS.
*/

$chapterauthor = "(?P<author>".$nombre."\s".$apellido." ".$role."((?:,\s|y\s))?)";
$chapterauthors = 'En\s'.$chapterauthor.'+'.$roles;
$chapteredicion = "(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.";
$chapterbook = "(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s".$chapteredicion."\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$";
$chaptername = '(?P<chapter>[A-Z][A-Za-zÀ-ÿ\s]+\.)';
$chapter = '/'.$chaptername.'\s'.$chapterauthors.$chapterbook.'/';


//-----------JOURNAL-------------//

/* annotations: 
 -> NO ACEPTA PUNTOS EL TITULO. SOLO ACEPTA PUNTOS EN LA REVISTA..
 -> NO RECONOCE NUMERO DE ARTICULO, SOLO "pp."
*/

$journal = '/(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s\:]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>\d{1,3})\((?P<volumen>\d{1,3})\)\,\s(?P<paginas>((?P<fpage>\d{1,4})–(?P<lpage>\d{1,4})))\./';

//--------------TESIS----------------//

$thesis = '/(?P<titulo>[A-Z][A-Za-zÀ-ÿ\s\:]+)\s\[(?P<comment>[^\][]*)\].\s/';

//-----------PAGINAS WEB-------------//




//----------------------------//
//           TESTS            //
//----------------------------//


/*
$textauthors = "Soler Maria, A. H., Rueda, O. y Morvillo, S. ()";
$authors_array = get_authors($textauthors,$authors);
print_r($authors_array);
*/

function match_date_expression($text) {
    $expressions = array(
        '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/' => 'datecomplete',
        '/(?P<fecha>\d{2}-\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/' => 'periodday',
        '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}-\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/' => 'periodmonth',
        '/(?P<fecha>\d{4}\/\d{4})/' => 'periodyear',
        '/(?P<fecha>\d{4})/' => 'year',
    );

    foreach ($expressions as $pattern => $name) {
        if (preg_match($pattern, $text, $matches)) {
            return array('expression' => $name, 'value' => $matches['fecha']);
        }
    }

    return array('expression' => 'No match found', 'value' => '');
}


function match_title_expression($text) {
    $expressions = array(
        '/(?P<chapter>[A-Z][A-Za-zÀ-ÿ\s]+\.)\sEn\s(?P<author>(?P<nombres>(\p{Lu}\.\s?)+)\s(?P<apellido>\p{L}+(\s\p{L}+)*) (?P<role>(\((Ed.|Coord.|Comp.)\)))?((?:,\s|y\s))?)+(?P<roles>(\((Eds.|Coords.|Comps.)\)))?(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>((?P<nedicion>[0-9]+ᵃ)\sed\.,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?)\))\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/' => 'chapter',
        '/(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s\((?P<edicion>(?P<nedicion>[0-9]+ª)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)\)\.)?\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)/' => 'book',
        '/(?P<journal>[A-Z][A-Za-zÀ-ÿ\s\:]+)\.\s(?P<revista>[A-Z][A-Za-zÀ-ÿ\s\:\.]+)\,\s(?P<nedicio>\d{1,3})\((?P<volumen>\d{1,3})\)\,\s(?P<paginas>(\d{1,4}-\d{1,4}))\./' => 'journal'
    );

    foreach ($expressions as $pattern => $name) {
        if (preg_match($pattern, $text, $matches)) {
            return array('expression' => $name, 'value' => $matches);
        }
    }

    return array('expression' => 'No match found', 'value' => '');
}

//$reference = "Apellido Autor, N. N. (1994). Título del trabajo. (3ª ed., Vol. 4). Editorial.";
//$reference = "Castaneda Naranjo, L. A. y Palacios Neri, J. (2015). Nanotecnología: fuente de nuevos paradigmas. Mundo Nano. Revista Interdisciplinaria en Nanociencias y Nanotecnología, 7(12), 45-49. https://doi.org/10.22201/ceiich.24485691e.2014.12.49710";
$reference = 'Renteria Salazar, P. (2006). El comienzo de la renovacion. En M. A. Florez Gongora (Ed.), Bogota Renovacion Urbana Renovacion Humana (pp. 80-100). Empresa De Renovacion Urbana.';
$authors_array = get_authors($reference,$authors);
$date = match_date_expression($reference);
$title = match_title_expression($reference);

print_r($authors_array);


echo "Expresión que coincidió: " . $date['expression'] . "\n";
echo "Fecha capturada: " . $date['value']. "\n";

echo "Expresión que coincidió: " . $title['expression'] . "\n";
if(strcmp($title['expression'],'book') == 0){

    echo "Titulo: " . $title['value']['book'] . "\n";
    echo "Edicion: " . $title['value']['edicion'] . "\n";
    echo "Editorial: " . $title['value']['editorial'] . "\n";

} 
if (strcmp($title['expression'],'journal') == 0){

    echo "journal: " . $title['value']['journal'] . "\n";
    echo "revista: " . $title['value']['revista'] . "\n";
    echo "nedicio: " . $title['value']['nedicio'] . "\n";
    echo "volumen: " . $title['value']['volumen'] . "\n";
    echo "paginas: " . $title['value']['paginas'] . "\n";

}
if (strcmp($title['expression'],'chapter') == 0){

    echo "chapter: " . $title['value']['chapter'] . "\n";
    echo "book: " . $title['value']['book'] . "\n";
    echo "edicion: " . $title['value']['edicion'] . "\n";
    echo "editorial: " . $title['value']['editorial'] . "\n";

    

}


?>
