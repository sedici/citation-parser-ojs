<?php
include_once 'Reference.php';

class JATSReference {
    public $reference;
    public const JATS_REF_ID_PREFIX = 'parser_ref';
    private $dom;
    private $ref;
    private $element_citation;
    private $mixed_citation;

    public function __construct(Reference $reference) {
        $this->reference = $reference;
        $this->dom = new DOMDocument('1.0', 'UTF-8');

        // Crear el elemento raíz <ref> con el prefijo de ID
        $this->ref = $this->dom->createElement('ref');
        $this->ref->setAttribute('id', self::JATS_REF_ID_PREFIX );
        $this->dom->appendChild($this->ref);

        $this->element_citation = $this->dom->createElement('element_citation');
        $this->element_citation->setAttribute('publication_type','journal');
        $this->mixed_citation = $this->dom->createElement('mixed_citation');
        $this->ref->appendChild($this->element_citation);
        $this->ref->appendChild($this->mixed_citation);


        $this->addDate();
        $this->addTitle();

    }

    public function getJatsXML() {
        // Devolver el XML como una cadena
        return $this->dom->save('jats.xml');
    }

    public function setAuthors($authores){
        $authorElement = $this->dom->createElement('person-group');
        $authorElement->setAttribute('person-group-type','author');
        $this->element_citation->appendChild($authorElement);
        
        $authores = [
            ["nombre" => "Gabriel", "apellido" => "García Márquez"],
            ["nombre" => "Isabel", "apellido" => "Allende"],
            ["nombre" => "Jorge", "apellido" => "Luis Borges"],
        ];
        

        foreach ($authores as $autor) {
            $nameElement = $this->dom->createElement('name');
            $surname = $this->dom->createElement('surname',$autor['apellido']);
            $given_names = $this->dom->createElement('given-names',$autor['nombre']);
            $nameElement->appendChild($surname);
            $nameElement->appendChild($given_names);
            $authorElement->appendChild($nameElement);
        }
        
    }

    public function addDate() {
        // Devolver el XML como una cadena
        $datePrinter = new DatePrinter($this->reference->getDate(),$this->dom);
        $elements = $datePrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
           
    }

    public function addTitle(){

        /*if($typeTitle == null) return;
        $printerClassName = ucfirst($type).'Printer';*/

        $bookPrinter = new JournalPrinter($this->reference->getTitle(), $this->dom);
        $elements = $bookPrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
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
