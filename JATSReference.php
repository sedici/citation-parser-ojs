<?php
include_once 'Reference.php';
class JATSReference {

    public $reference;
    public const JATS_REF_ID_PREFIX = 'parser_';
    private $dom;
    private $ref;
    private $element_citation;
    private $mixed_citation;

    public function __construct(Reference $reference,\DOMDocument $dom = null, int $id = 0) {
        $this->reference = $reference;
        $this->dom = $dom ?? new \DOMDocument('1.0', 'UTF-8');

        // Crear el elemento raíz <ref> con el prefijo de ID
        $this->ref = $this->dom->createElement('ref');
        $this->ref->setAttribute('id', self::JATS_REF_ID_PREFIX.$id );
        $this->dom->appendChild($this->ref);
        
        $this->mixed_citation = $this->dom->createElement(localName: 'mixed_citation',value: $this->reference->getPlainTextReference());

        $this->element_citation = $this->dom->createElement('element_citation');
        $this->element_citation->setAttribute(qualifiedName: 'publication_type',value: $this->reference->getTitleType());

        $this->ref->appendChild($this->element_citation);
        $this->ref->appendChild($this->mixed_citation);

    }

    public function createXMLElemetns(){
        $this->addAuthors();
        $this->addDate();
        $this->addTitle();
        $this->addURL();
    }

    public function getJatsXML(string $rout = null) {
        // Devolver el XML como una cadena
        $this->createXMLElemetns();
        $this->dom->preserveWhiteSpace = false; // Opcional, para evitar espacios en blanco innecesarios
        $this->dom->formatOutput = true; // Activar la salida formateada
        return $this->dom->save($rout);
    }

    public function addAuthors() {
        // Devolver el XML como una cadena
        $authorPrinter = new AuthorPrinter($this->reference->getAuthor(),$this->dom);
        $elements = $authorPrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
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


    public function addURL() {
        // Devolver el XML como una cadena
        $urlType = $this->reference->getURLType(); 
        if ($urlType === null || trim($urlType) === "" || $urlType === "No match found") {
            return;
        }

        $printerClassName = ucfirst($urlType).'Printer';
        $printer = new $printerClassName($this->reference->getURL(), $this->dom);

        $elements = $printer->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
           
    }

    public function addTitle(){

        $titleType = $this->reference->getTitleType(); 
        if ($titleType === null || trim($titleType) === "" || $titleType === "No match found") {
            $errorComment = $this->dom->createComment('Error: Failed to parse the title information. Please review.');
            $this->element_citation->appendChild($errorComment);
            return;
        }

        $printerClassName = ucfirst($titleType).'Printer';
        $printer = new $printerClassName($this->reference->getTitle(), $this->dom);
        $elements = $printer->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
    }

    public function getDoi(): ?string {
        $urlType = $this->reference->getURLType();
        // Verificar si el tipo de URL es 'doi', si no, retornar null
        if ($urlType !== 'DOI') {
            return null;
        }
        // Retornar el DOI si el tipo de URL es correcto
        return $this->reference->getURL()['doi'];
    }
    
    public function getInstitutions(): ?string {
        return null;
    }



}
?>
