<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'GenericPrinter.php';
include_once 'OpenAlexApi/EnrichmentInstitutionInterface.php';
include_once 'OpenAlexApi/OpenAlexApi.php';

class ThesisPrinter extends TitlePrinter implements EnrichmentInstitutionInterface{

    public function toPlainText(): string{
        return "";
    }

    /** Creates the XML elements representing the Thesis in JATS format.
     * @return array An array of XML elements.
     */
    public function createXMLElements(): array {
        $elements = [];

        $sourceElement = $this->createElement('article-title',$this->getSource());
        $elements[] = $sourceElement;

        $commentElement = $this->createElement('comment',$this->getComment());
        $elements[] = $commentElement;

        $publisherLocElement = $this->createElement('publisher-loc',$this->getPublisherLoc());
        $elements[] = $publisherLocElement;

        $publisherName = $this->getPublisherName();
        if (!empty($publisherName)) {
            $publisherNameElement = $this->createElement('publisher-name',$publisherName );
            $elements[] = $publisherNameElement;

            $result = $this->enrichInstitutionData($publisherName);
            if ($result) $elements[] = $result;
        }
        
        return $elements;
    }

    public function getSource(){
        return $this->getTitle();
    }

    public function getComment(){
        return $this->get('comment');
    }

    public function getPublisherName(){
        return $this->get('publisher-name');
    }

    public function getPublisherLoc(){
        return $this->get('publisher-loc');
    }

    public function enrichInstitutionData(string $name) {
        try {
            $api = new OpenAlexAPI();
            $response = $api->searchInstitutions($name);
    
            if (!isset($response['ror']) || !isset($response['display_name']) || !isset($response['type'])) {
                return false;
            }
    
            $institutionWrap = $this->dom->createElement('institution-wrap');
    
            $institutionId = $this->createElement('institution-id', $response['ror']);
            $institutionId->setAttribute('institution-id-type', "ROR");
            $institutionWrap->appendChild($institutionId);
    
            $institution = $this->createElement('institution', $response['display_name']);
            $institution->setAttribute('content-type', $response['type']);
            $institutionWrap->appendChild($institution);
    
            return $institutionWrap;
    
        } catch (\Exception $e) {
            return false;
        }
    }
    
}