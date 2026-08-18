<?php

/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once 'Reference.php';
require_once __DIR__ . '/Printer/OpenAlexApi/Enrichers/OpenAlexEnricherFactory.php';

class JATSReference {

    public $reference;
    public const JATS_REF_ID_PREFIX = 'parser_';
    private $dom;
    private $reflist;
    private $ref;
    private $element_citation;
    private $mixed_citation;

    private $enrichmentData = []; //Set an empty array if there is no DOI in OpenAlex and an array with data to enrich the JATS XML if that DOI exists in OpenAlex

    private $openAlexSourceType = null;
    private $enricherUsedClass = null;
    private bool $isFallbackEnricher = false;

    private $errors = "";
    private bool $enrichmentHadErrors = false; 


    public function __construct(?\DOMDocument $dom = null, ?\DOMElement $reflist = null, Reference $reference, int $id = 0) {
        $this->reference = $reference;
        $this->dom = $dom ?? new \DOMDocument('1.0', 'UTF-8');
        $this->reflist = $reflist;
        // Crear el elemento raíz <ref> con el prefijo de ID
        $this->ref = $this->dom->createElement('ref');
        $this->ref->setAttribute('id', self::JATS_REF_ID_PREFIX.$id );
        $this->reflist->appendChild($this->ref);
        
        $this->mixed_citation = $this->dom->createElement('mixed-citation',$this->reference->getPlainTextReference());

        $this->element_citation = $this->dom->createElement('element-citation');
        $this->element_citation->setAttribute('publication-type',$this->reference->getTitleType());

        $this->ref->appendChild($this->element_citation);
        $this->ref->appendChild($this->mixed_citation);

    }

    //If we have errors, we need to delete element-citation tag. We create a comment in mixed-citation tag with these errors.
    public function checkErrors(): void{
        // If we have enrichment data and no errors during enrichment
        // We suppress previous parsing errors to avoid dirtying the final XML.
        if (!empty($this->enrichmentData) && $this->enrichmentHadErrors === false) {
            $this->errors = ""; // no imprimir ni propagar errores si el enriquecimiento salió bien
            return;
        }

        if (trim($this->errors) !== "") {
            $textError = 'ERRORS FOUND IN THESE SECTIONS: "' . $this->errors . '"';
            $this->mixed_citation->nodeValue .= " --- " . $textError;
            $this->ref->removeChild($this->element_citation);
        }
    }

    public function createXMLElemetns(){
        $this->addAuthors();
        $this->addDate();
        $this->addTitle();
        $this->addURL();
        $this->enrichment();
        $this->checkErrors();
    }

    public function getJatsXML() {
        // Devolver el XML como una cadena
        $this->createXMLElemetns();
    }

    public function addError(String $errorText): String{
        return $this->errors .= $errorText;
    }

    public function addAuthors() {
        // Return xml as a string

        $authorType = $this->reference->getAuthorType();
        if ($authorType === null || trim($authorType) === "" || $authorType === "No match found") {
            $this->addError("Author. ");
            return;
        }

        $authorPrinter = new AuthorPrinter($this->reference->getAuthor(),$this->dom);
        $elements = $authorPrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
    }

    public function addDate() {
        // Return xml as a string

        $dateType = $this->reference->getDateType();
        if ($dateType === null || trim($dateType) === "" || $dateType === "No match found") {
            $this->addError("Date. ");
            return;
        }

        $datePrinter = new DatePrinter($this->reference->getDate(),$this->dom);
        $elements = $datePrinter->createXMLElements();
        foreach ($elements as $element) {
            $this->element_citation->appendChild($element);
        }
           
    }

    public function addURL() {
        // Return xml as a string
        
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

    public function addTitle(){     
        //Return xml as a string

        $titleType = $this->reference->getTitleType(); 
        if ($titleType === null || trim($titleType) === "" || $titleType === "No match found") {
            $this->addError("Title. ");
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

    public function getInstitution(): ?string {
        return $this->reference->getAuthor()['institution'] ?? null;
    }

    public function setEnrichmentData(Array $enrichmentData) {
        // Inject original reference authors so printers can apply the same strategy when building JATS
        $this->enrichmentData = $enrichmentData;
    }

    public function getOpenAlexSourceType(): ?string {
        return $this->openAlexSourceType;
    }

    public function getEnricherUsed(): ?string {
        return $this->enricherUsedClass;
    }

    public function isFallbackEnricher(): bool {
        return $this->isFallbackEnricher;
    }

    /**
     * Enrich the JATS reference with data from OpenAlex
     * If there is enrichment data, it will add or replace elements in the element-citation tag
     * If there is no enrichment data, it will not modify the element-citation tag
     * If there are errors, it will remove the element-citation tag and add a comment in the mixed-citation tag
     * @return void
     */
    public function enrichment() {
        if (empty($this->enrichmentData)) { return; }

        $resolved = OpenAlexEnricherFactory::resolve($this->enrichmentData);
        $this->openAlexSourceType = $resolved['source_type'];
        $this->enricherUsedClass = $resolved['enricher_class'];
        $this->isFallbackEnricher = $resolved['is_fallback'];

        $publicationType = $this->element_citation->getAttribute('publication-type');
        if (empty($publicationType)) {
            $this->element_citation->setAttribute('publication-type', strtolower($this->openAlexSourceType));
        }

        $elements = $resolved['enricher']->enrich($this->dom, $this->enrichmentData);

        foreach ($elements as $newElement) {
            $tag = $newElement->tagName;
            $existing = $this->element_citation->getElementsByTagName($tag)->item(0);
            if ($existing) {
                $this->element_citation->replaceChild($newElement, $existing);
            } else {
                $this->element_citation->appendChild($newElement);
            }
        }
        $this->ref->appendChild($this->element_citation);
    }



}

?>
