<?php

/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/


include_once 'Printer/OpenAlexApi/OpenAlexApi.php';
include_once 'Printer/OpenAlexApi/OpenAlexApiManager.php';
require_once 'Reference.php';
require_once 'JATSReference.php';

class ReferencesManager {
    private $refs;
    private array $jatsList = [];
    private array $jatsWithDoi = [];

    public \DOMDocument $dom;
    public \DOMElement $back;

    public $reflist;

    private OpenAlexApiManager $oam;

    public function __construct(\DOMDocument $dom = null,\DOMElement $back = null,array $refs = null) {
        $this->dom = $dom ?? new \DOMDocument('1.0', 'UTF-8');
        $this->back = $back;
        $this->refs = $refs;

        $this->reflist = $this->dom->createElement('ref-list');
        $this->back->appendChild($this->reflist);

        $this->oam = new OpenAlexApiManager();

        $this->process();
    }

    /**
     * Process the references and generate JATS XML.
     * This method iterates through the references, creates JATSReference objects,
     * and generates the corresponding XML.
     * @return void
     */
    public function process() {
        foreach ($this->refs as $index => $ref) {
            $reference = new Reference($ref);
            $jats = new JATSReference($this->dom, $this->reflist, $reference, $index);
            $this->jatsList[] = $jats;

            $doi = $jats->getDoi();
            if ($doi) {
                $this->oam->addDoi($doi);
                $this->jatsWithDoi[$doi] = $jats;
            }
        }

        $this->generateXML();
    }

    /**
     * Generate the JATS XML for all references.
     * This method calls the getJatsXML method on each JATSReference object
     * to build the complete XML structure.
     * @return void
     */
    private function generateXML() {
        foreach ($this->jatsList as $jats) {
            $jats->getJatsXML();
        }
    }
}