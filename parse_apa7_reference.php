<?php

//----------------------------//
//       AUTOR/EDITOR         //
//----------------------------//  

$author = '/^(?P<apellidos>([A-Za-zÀ-ÿáéíóú]+\s?)+),\s(?P<nombres>([A-Za-z]\.\s)+)$/';
$twoauthor = '/^(?P<apellidos1>([A-Za-zÀ-ÿáéíóú]+\s?)+),\s(?P<nombres1>([A-Za-z]\.\s?)+)(y\s(?P<apellidos2>([A-Za-zÀ-ÿáéíóú]+\s?)+),\s(?P<nombres2>([A-Za-z]\.\s?)+))$/';   
$manyauthors = '/^(?P<autor>(?P<apellidos>([A-Za-zÀ-ÿáéíóú]+\s?)+),\s(?P<nombres>[A-Za-z]\.\s,)+)+$/';

//--------------------------------------------//
//            DATE OF PUBLICATION             //
//--------------------------------------------//

//Recupera el valor que va dentro de los parentesis que representan la fecha. No chequea que este mal o bien
$daterecolector = '/\.\s\((?P<fecha>(?>(\d.[^)]*|s\.f\.[a-z]?)))/';
    $edicion = "/(?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\sVol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?)/";
    // 1ᵃ ed., Vol. 5 | 1ᵃ ed., Vol. III | 1ᵃ ed.

$year = '/(?P<fecha>\d{4}(\/\d{4})?)/';
// 2023 o 2023/2024

$datecomplete = '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/'; 
// 15 de abril de 2024

$periodday = '/(?P<fecha>\d{2}-\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/';
// 15-17 de abril de 2024

$periodmonth = '/(?P<fecha>\d{2}\sde\s[a-z]{5,10}-\d{2}\sde\s[a-z]{5,10}\sde\s\d{4})/';
// 31 de marzo-2 de abril de 2024

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
$chapter = '/^(?P<chapter>[A-Z][A-Za-zÀ-ÿ\s]+\.)\sEn.\s,(?P<book>[A-Z][A-Za-zÀ-ÿ\s]+)\s(\((?P<edicion>(?P<nedicion>[0-9]+ᵃ)\sed\.(,\s)?(Vol\.\s(?P<volumen>(?:[IVXLCDM]+|[0-9]+)))?(,\s)?(pp.\s(?P<paginas>(\d{1,4}-\d{1,4})))?))\)\.\s(?P<editorial>[A-Z][A-Za-zÀ-ÿ\s]+\.)(\s(?P<url>https?:\/\/[^\s]+))?$/'
//falta autores

// 2) Título de periódicos, boletines y revistas
// 3) Título de libros e informes




//----------------------------//
//           TESTS            //
//----------------------------//

/*
$texto = "El Quijote. (1ᵃ ed., Vol. 4). Editorial Cátedra. https://google.com";

preg_match($bookurl, $texto, $coincidencias);

if (!empty($coincidencias)) {
    echo "Título: " . $coincidencias['titulo'] . PHP_EOL;
    echo "Edición: " . $coincidencias['edicion'] . PHP_EOL;
    echo "Editorial: " . $coincidencias['editorial'] . PHP_EOL;
    echo "Url: " . $coincidencias['url'] . PHP_EOL;
} else {
    echo "No se encontró ninguna coincidencia." . PHP_EOL;
}
*/


?>
