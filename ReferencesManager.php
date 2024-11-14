<?php
include_once 'Printer/OpenAlexApi/OpenAlexApi.php';
include_once 'Printer/OpenAlexApi/OpenAlexApiManager.php';
require_once 'Reference.php';
require_once 'JATSReference.php';

class ReferencesManager {
    private $refs;
    private array $jatsList = [];
    private array $jatsRefListWithDoi = [];
    public \DOMDocument $dom;
    private OpenAlexApiManager $oam;

    public function __construct(string $refs = null) {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
        $this->refs = file('examples/ayana/15096/ayana.15096.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->oam = new OpenAlexApiManager();
    }

    public function process() {
        // Procesar cada referencia
        foreach ($this->refs as $index => $ref) {
            $reference = new Reference($ref);
            $jats = new JATSReference($reference, $this->dom, $index);
            $this->jatsList[] = $jats;

            // Si la referencia tiene DOI, agregar al manager de OpenAlex
            $doi = $jats->getDoi();
            $institutions = $jats->getinstitutions();
            if ($doi) {
                
                $this->oam->addDoi($doi);
                $this->jatsRefListWithDoi[$doi] = $jats;

            } else if ($institutions) {       
                $this->oam->addInstitutios($institutions);
                $this->jatsRefListWithInstitutions[$institutions] = $jats;
            }

        }

        $this->openAlexRequest();
        $this->generateXML();
    }

    private function openAlexRequest() {
       // Implementación de la solicitud a OpenAlex si es necesario
        $oar = $this->oam->request();
        $this->enrichmenteJatsRefElement($oar);
        return null;
    }

    private function enrichmenteJatsRefElement(){
        foreach ($this->jatsRefListWithDoi as $index => $jats) {
            if ($oar[$index]){
                $jats->enrichmente($oar[$index]);
            } 
        }
    }

    private function generateXML() {
        foreach ($this->jatsList as $jats) {
            $jats->getJatsXML('reports/output.xml');
        }
        //$this->dom->save('reports/output.xml');
    }
}

$manager = new ReferencesManager();
$manager->process();
