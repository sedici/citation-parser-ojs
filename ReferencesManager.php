<?php
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

    public function process() {
        // Procesar cada referencia
        foreach ($this->refs as $index => $ref) {
            $reference = new Reference($ref);
            $jats = new JATSReference($this->dom, $this->reflist, $reference, $index);
            $this->jatsList[] = $jats;

            // Si la referencia tiene DOI, agregar al manager de OpenAlex
            $doi = $jats->getDoi();
            $institutions = $jats->getinstitutions();
            if ($doi) {
                
                $this->oam->addDoi($doi);
                $this->jatsWithDoi[$doi] = $jats;

            } else if ($institutions) {       
                $this->oam->addInstitutios($institutions);
                $this->jatsRefListWithInstitutions[$institutions] = $jats;
            }

        }

        //$this->openAlexRequest();
        $this->generateXML();
    }

    private function generateXML() {
        foreach ($this->jatsList as $jats) {
            $jats->getJatsXML();
        }
        //$this->dom->save();
    }

    private function openAlexRequest() {
        // Implementación de la solicitud a OpenAlex si es necesario
         $oar = $this->oam->request();
         $this->enrichmenteJatsRefElement($oar);
         return null;
     }
 
     private function enrichmenteJatsRefElement(){
         foreach ($this->jatsWithDoi as $index => $jats) {
             if ($oar[$index]){
                 $jats->enrichmente($oar[$index]);
             } 
         }
     }
 
}