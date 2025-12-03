<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'URLPrinter.php';
class DOIPrinter extends URLPrinter{

    /**
     * Create XML elements for the DOI.
     * @return array An array of XML elements representing the DOI.
     */
    public function createXMLElements(): array {
        $elements = parent::createXMLElements();

        $pub_id = $this->createElement('pub-id', $this->getDOI());
        $pub_id->setAttribute('pub-id-type','doi');
        $elements[] = $pub_id;
        
        return $elements;
    }

    public function getDOI(): string{
        return $this->get('doi');
    }

    public function getPrefix(): string{
        return $this->get('prefix');
    }

    public function getSufix(): string{
        return $this->get('sufix');
    } 
    
    public function enrichDoiData(string $doi) {
        try {
            if (empty($doi)) return false;
            
            $api = new OpenAlexAPI();
            $response = $api->searchWorksWhitDoi($doi);
    
            if (!isset($response)) {
                return false;
            }
            
            $enrichDoiData = [];

            $title = $this->dom->createElement('title', $response['title']);
            $source_display_name = $this->dom->createElement('source_display_name', $response['source_display_name']);
            $source_issn_l = $this->dom->createElement('issn_l', $response['source_issn_l']);

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