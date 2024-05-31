<?php
include_once 'Reference.php';

class JATSReference {
    public $reference;
    public const JATS_REF_ID_PREFIX = 'parser_ref';
    private $domDocument;
    private $ref;
    private $element_citation;
    private $mixed_citation;

    public function __construct(Reference $reference) {
        $this->reference = $reference;
        $this->domDocument = new DOMDocument('1.0', 'UTF-8');

        // Crear el elemento raíz <ref> con el prefijo de ID
        $this->ref = $this->domDocument->createElement('ref');
        $this->ref->setAttribute('id', self::JATS_REF_ID_PREFIX );
        $this->domDocument->appendChild($this->ref);

        $this->element_citation = $this->domDocument->createElement('element_citation');
        $this->element_citation->setAttribute('publication_type','journal');
        $this->mixed_citation = $this->domDocument->createElement('mixed_citation');
        $this->ref->appendChild($this->element_citation);
        $this->ref->appendChild($this->mixed_citation);

        $this->setAuthors('arreglo de autores');
        $this->setDate('1990','pub');
        $this->setTitle($reference->getTitle(),$reference->getTitleType());
        $this->domDocument->formatOutput = true;

    }

    public function getJatsXML() {
        // Devolver el XML como una cadena
        return $this->domDocument->save('jats.xml');
    }

    public function setAuthors($authores){
        $authorElement = $this->domDocument->createElement('person-group');
        $authorElement->setAttribute('person-group-type','author');
        $this->element_citation->appendChild($authorElement);
        
        $authores = [
            ["nombre" => "Gabriel", "apellido" => "García Márquez"],
            ["nombre" => "Isabel", "apellido" => "Allende"],
            ["nombre" => "Jorge", "apellido" => "Luis Borges"],
        ];
        

        foreach ($authores as $autor) {
            $nameElement = $this->domDocument->createElement('name');
            $surname = $this->domDocument->createElement('surname',$autor['apellido']);
            $given_names = $this->domDocument->createElement('given-names',$autor['nombre']);
            $nameElement->appendChild($surname);
            $nameElement->appendChild($given_names);
            $authorElement->appendChild($nameElement);
        }
        
    }

    public function setDate($date,$dateTye) {
        // Devolver el XML como una cadena

        $dateElement = $this->domDocument->createElement('date');
        $dateElement->setAttribute('iso-8601-date', $date);
        $dateElement->setAttribute('date-type', 'pub');
        $this->domDocument->appendChild($dateElement);

        $yearElement = $this->domDocument->createElement('year',$date);
        $dateElement->appendChild($yearElement);

        $monthElement = $this->domDocument->createElement('month',$date);
        $dateElement->appendChild($monthElement);

        $monthElement = $this->domDocument->createElement('day',$date);
        $dateElement->appendChild($monthElement);

        $this->element_citation->appendChild($dateElement);

        
           
    }

    public function setTitle($title,$typeTitle){
        /*
        if ($title['type'] == 'journal') {
            $article = $this->domDocument->createElement('article-title','article title example');
        }

        if ($title['type'] == 'book') {
            $publisher = $this->domDocument->createElement('publisher','publisher example');
        }

        if ($title['type'] == 'congress') {
            $conf = $this->domDocument->createElement('conf-name','conf-name example');
        } 
        */
        if($typeTitle == null) return;
        $printerClassName = ucfirst($type).'Printer';
        $sourceString = $printerClassName::getSource($title);

        $source = $this->domDocument->createElement('source',$sourceString);
        $this->element_citation->appendChild($source);
    }



}
/*


$journal = 'Castakeda Naranjo, L. A. y Palacios Neri, J. (2015). Nanotecnología: fuente de nuevos paradigmas. Mundo Nano. Revista Interdisciplinaria en Nanociencias y Nanotecnología, 7(12), 45–49.';
$jounralReference = new Reference($journal);
$jounralReference->print();*/
// Ejemplo de uso
/*
$jatsReference = new JATSReference($jounralReference);
$jatsReference->getJatsXML();
*/
?>
