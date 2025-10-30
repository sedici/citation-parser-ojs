<?php

/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once 'Printer/OpenAlexApi/OpenAlexApi.php';
include_once 'Printer/OpenAlexApi/OpenAlexApiManager.php';
require_once 'Reference.php';
require_once 'JATSReference.php';
include_once 'validators/AuthorValidator.php';

class ReferencesManager {
    private $refs;
    private array $jatsList = [];
    private array $jatsWithDoi = [];

    public \DOMDocument $dom;
    public \DOMElement $back;

    public $reflist;

    private AuthorValidator $authorValidator;

    private OpenAlexApiManager $oam;

    public function __construct(?\DOMDocument $dom = null, ?\DOMElement $back = null, ?array $refs = null) {
        $this->dom = $dom ?? new \DOMDocument('1.0', 'UTF-8');
        $this->back = $back;
        $this->refs = $refs;

        $this->reflist = $this->dom->createElement('ref-list');
        $this->back->appendChild($this->reflist);

        $this->oam = new OpenAlexApiManager();

        $this->authorValidator = new AuthorValidator();

        $this->process();
    }

    public function process() {
        // Procesar cada referencia
        foreach ($this->refs as $index => $ref) {
            $parsedReference = new Reference($ref);
            $jats = new JATSReference($this->dom, $this->reflist, $parsedReference, $index);
            $this->jatsList[] = $jats;

            // Si la referencia tiene DOI, agregar al manager de OpenAlex
            $doi = $jats->getDoi();
            $institution = $jats->getInstitution();
            if ($doi) {
                $this->oam->addDoi($doi);
                $this->jatsWithDoi[$doi] = $jats;

            } else if ($institution) {
                $this->processInstitution($institution, $jats);
            }
        }

        $this->openAlexRequest();
        $this->generateXML();
    }

    private function generateXML() {
        foreach ($this->jatsList as $jats) {
            $jats->getJatsXML();
        }
        //$this->dom->saveXML();

        file_put_contents(
            __DIR__ . '/Printer/testJats/testJats.log',
            print_r($this->dom->saveXML(), true)
        );
    }

    private function openAlexRequest() {
        // Implementación de la solicitud a OpenAlex si es necesario
         $jsonDoiOar = $this->oam->doiRequest();
         $decodedDoiOar = json_decode($jsonDoiOar, true);
         $this->enrichmentJatsRefElement($decodedDoiOar);

         return null;
     }

    private function enrichmentJatsRefElement($oar){
        $results = $oar['results'] ?? [];
        
        foreach ($this->jatsWithDoi as $doi => $jats) {
            $found = false;
            foreach ($results as $index => $result) {
                if (strpos($result['doi'],  $doi) !== false) {
                    $found = true;
                    if ($this->authorValidator->validateFullNameAsAuthor($result, $jats)) {
                        // Preprocesar autores una sola vez para que los printers solo impriman
                        $formattedAuthors = $this->authorValidator->buildFormattedAuthors($result, $jats);
                        $result['__authors_formatted'] = $formattedAuthors;
                        $jats->setEnrichmentData($result);
                    }
                    break;
                }
            }

            if (!$found) {
                $jats->addError('The DOI ' . $doi . ' does not exist in OpenAlex database.');
            }
        }
    }
 
    private function processInstitution(String $institution, ?JATSReference $jats = null) {
        if (!$jats) {
            return null;
        }
        $request = $this->oam->searchInstitution($institution);
        $openAlexResults = $request['results'] ?? [];
        $this->authorValidator->validateInstitutionAsAuthor($openAlexResults, $jats, $institution);
    }

}