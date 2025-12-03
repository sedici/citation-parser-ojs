<?php

/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once 'Reference.php';
class JATSReference {

    public $reference;
    public const JATS_REF_ID_PREFIX = 'parser_';
    private $dom;
    private $ref;
    private $element_citation;
    private $mixed_citation;

    private $errors = "";

    public function __construct(\DOMDocument $dom = null,\DOMElement $reflist = null,Reference $reference,int $id = 0) {
        $this->reference = $reference;
        $this->dom = $dom ?? new \DOMDocument('1.0', 'UTF-8');
        $this->reflist = $reflist;
        
        $this->ref = $this->dom->createElement('ref');
        $this->ref->setAttribute('id', self::JATS_REF_ID_PREFIX.$id );
        $this->reflist->appendChild($this->ref);
        
        $this->mixed_citation = $this->dom->createElement('mixed-citation',$this->reference->getPlainTextReference());

        $this->element_citation = $this->dom->createElement('element-citation');
        $this->element_citation->setAttribute('publication-type',$this->reference->getTitleType());

        $this->ref->appendChild($this->element_citation);
        $this->ref->appendChild($this->mixed_citation);

    }

    /** 
     * Creates the XML elements for the reference.
     * This method orchestrates the addition of authors, date, title, and URL to the element-citation node.
     * It also checks for errors after all elements have been added.
     * @return void
     */
    public function createXMLElemetns(){
        $this->addAuthors();
        $this->addDate();
        $this->addTitle();
        $this->addURL();
        $this->checkErrors();
    }

    /** 
     * Returns the JATS XML representation of the reference.
     * This method is a placeholder and should be implemented to return the XML structure.
     * @return void
     */
    public function getJatsXML() {
        $this->createXMLElemetns();
    }

    /**
     * Check for errors in the reference and update the mixed-citation element accordingly.
     * If errors are found, they are appended to the mixed-citation node and the element-citation node is removed from the reference.
     * If no errors are found, the mixed-citation node remains unchanged.
     * @return void
     */
    public function checkErrors(){
        if (trim($this->errors) !== "") {
            
            $textError = 'ERRORS FOUND IN THESE SECTIONS: "' . $this->errors . '"';

            $this->mixed_citation->nodeValue .= " --- " . $textError;
            $this->ref->removeChild($this->element_citation);
        }
    }

    public function addError(String $errorText): String{
        return $this->errors .= $errorText;
    }

    /** Adds authors to the element-citation node.
     * If the author type is null, empty, or "No match found", an error is added and the method returns.
     * Otherwise, it creates an AuthorPrinter instance and appends the resulting XML elements to the element-citation node.
     * @return void
     */
    public function addAuthors() {
        $authorType = $this->reference->getAuthorType();
        if ($authorType === null || trim($authorType) === "" || $authorType === "No match found") {
            $errorText = "Author. ";
            $this->addError($errorText);
            return;
        }
        $authorType = $this->reference->getAuthorType();
        $authorPrinter = new AuthorPrinter($this->reference->getAuthor(),$this->dom);
        $elements = $authorPrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
    }

    /** Adds the date to the element-citation node.
     * If the date type is null, empty, or "No match found", an error is added and the method returns.
     * Otherwise, it creates a DatePrinter instance and appends the resulting XML elements to the element-citation node.
     * @return void
     */
    public function addDate() {
        $dateType = $this->reference->getDateType();
        if ($dateType === null || trim($dateType) === "" || $dateType === "No match found") {
            $errorText = "Date. ";
            $this->addError($errorText);
            return;
        }

        $datePrinter = new DatePrinter($this->reference->getDate(),$this->dom);
        $elements = $datePrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
           
    }

    /** Adds the URL to the element-citation node.
     * If the URL type is null, empty, or "No match found", the method returns without adding anything.
     * Otherwise, it creates a URLPrinter instance and appends the resulting XML elements to the element-citation node.
     * @return void
     */
    public function addURL() {        
        $urlType = $this->reference->getURLType(); 
        if ($urlType === null || trim($urlType) === "" || $urlType === "No match found. ") {
            return;
        }

        $printerClassName = ucfirst($urlType).'Printer';
        $printer = new $printerClassName($this->reference->getURL(), $this->dom);

        $elements = $printer->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
           
    }

    /** Adds the title to the element-citation node.
     * If the title type is null, empty, or "No match found", an error is added and the method returns.
     * Otherwise, it creates a TitlePrinter instance and appends the resulting XML elements to the element-citation node.
     * @return void
     */
    public function addTitle(){
        $titleType = $this->reference->getTitleType(); 
        if ($titleType === null || trim($titleType) === "" || $titleType === "No match found") {
            $errorText = "Title. ";
            $this->addError($errorText);
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
        if ($urlType !== 'DOI') {
            return null;
        }
        return $this->reference->getURL()['doi'];
    }
    
    public function getInstitutions(): ?string {
        return null;
    }
}
?>
