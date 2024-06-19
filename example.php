<?php
require_once 'Reference.php';
require_once 'JATSReference.php';
// Supongamos que tienes una referencia bibliográfica
$book = 'Herrera Caceres, C. y Rosillo Penia, M. (2019). Confort y eficiencia energética en el diseño de edificaciones. Universidad del Valle.';
$journal = 'Castakeda Naranjo, L. A. y Palacios Neri, J. (2015). Nanotecnología: fuente de nuevos paradigmas. Mundo Nano. Revista Interdisciplinaria en Nanociencias y Nanotecnología, 7(12), 45–49.';
$charapter = 'Apellido, A. y Apellido, B. (2019). Título del capítulo. En N. Apellido (Ed.), Título del libro (pp. 19-21). Editorial.';
$thesis = 'Ruiz, M. E. (10 de marzo de 2011). La intercambiabilidad de medicamentos: consideraciones biofarmacéuticas y terapéuticas [Tesis de Doctorado, Universidad Nacional de La Plata]. https://sedici.unlp.edu.ar/handle/10915/53595s';


$test = 'Abbott, W. (1925). A method of computing the effectiveness of an insecticide. Journal of Economic Entomology, 18, 265-267. http://doi.org/10.1093/jee/18.2.265a';
//no anda porque requiere obligatoriamente los parentesis de volumen

$test = "Echeverría, G. (2016). El libro electrónico en el escenario editorial. En C. Rogovsky (Comp.), Versiónfinal.doc. Aportes para la producción de textos en el ámbito académico (pp. 130-135). EDULP. https://sedici.unlp.edu.ar/handle/10915/53595";
$test = 'Bernal Meza, R. (2013). Heterodox autonomy doctrine Realism and purposes and its relevance. Revista Brasileña de Política Internacional, 56(2), 45-62.';


$testurl = "Echeverría, G. (2016). El libro electrónico en el escenario editorial. En C. Rogovsky (Comp.), Versiónfinal.doc. Aportes para la producción de textos en el ámbito académico (pp. 130-135). EDULP. https://sedici.unlp.edu.ar/handle/10915/53595";

// ----- book ------ //

$booktest = 'Escudé, C. (1992). El realismo periférico. Fundamentos para la nueva política exterior argentina. Planeta. ';
//Esta referencia no funciona por la "é". Ademas no fnciona por el punto y la coma en el titulo.
    $booktest = 'Escude, C. (1992). El realismo periférico fundamentos para la nueva política exterior argentina. Planeta. ';


$booktest = 'Figari, G. (1993). Pasado, presente y futuro de la política exterior argentina. Biblos.';



// ----- book ------ //

$test = 'Escudé, C. (1992). El realismo periférico. Fundamentos para la nueva política exterior argentina. Planeta. ';

//Esta referencia no funciona por la "é". Ademas no fnciona por el punto y la coma en el titulo.
    $test = 'Escude, C. (1992). El realismo periférico fundamentos para la nueva política exterior argentina. Planeta. ';


$dom = new \DOMDocument('1.0', 'UTF-8');
// Leer el archivo de texto
$referencias = file('Examples/ayana/14758/reference.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Procesar cada referencia
foreach ($referencias as $referencia) {
    $reference = new Reference($referencia);    
    $jats = new JATSReference($reference,$dom);
    $jats->getJatsXML();
}

$dom->save('output.xml');