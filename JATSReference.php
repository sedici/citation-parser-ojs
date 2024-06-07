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
        $this->element_citation->setAttribute('publication_type',$this->reference->getTitleType());
        $this->mixed_citation = $this->dom->createElement('mixed_citation');
        $this->ref->appendChild($this->element_citation);
        $this->ref->appendChild($this->mixed_citation);

        $this->addAuthors();
        $this->addDate();
        $this->addTitle();

    }

    public function getJatsXML(string $rout = 'output.xml') {
        // Devolver el XML como una cadena
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

    public function addTitle(){

        $titleType = $this->reference->getTitleType(); 
        if($titleType == null) return;

        $printerClassName = ucfirst($titleType).'Printer';
        $printer = new $printerClassName($this->reference->getTitle(), $this->dom);
        $elements = $printer->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
    }



}
?>
