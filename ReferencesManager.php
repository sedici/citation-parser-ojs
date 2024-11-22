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

    public function process($output = 'output.xml') {
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
        $this->generateXML($output);
    }

    private function generateXML(string $rout = null) {
        foreach ($this->jatsList as $jats) {
            $jats->getJatsXML($rout);
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
/*
$dom = new \DOMDocument('1.0', 'UTF-8');
$article = $dom->createElement('article');
$article->setAttributeNS(
    "http://www.w3.org/2000/xmlns/",
    "xmlns:xlink",
    "http://www.w3.org/1999/xlink"
);
$dom->appendChild($article);
$back = $dom->createElement('back');
$article->appendChild($back);

$ref = file('examples/ayana/15096/ayana.15096.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$manager = new ReferencesManager($dom,$back,$ref);
$manager->process('reports/output.xml');
*/