<?php
include_once 'Printer/OpenAlexApi/OpenAlexApi.php';
include_once 'Printer/OpenAlexApi/OpenAlexApiManager.php';
require_once 'Reference.php';
require_once 'JATSReference.php';
class ReferencesManager{

    private string $refListPlanText; 
    private array $jatsRefList;
    private array $jatsRefListWhithDoi;
    private OpenAlexApiManager $oam;

    public function __construct(string $refListPlanText = null){
        $this->$refListPlanText = $refListPlanText;
        $this->oam = new OpenAlexApiManager();
    }

    public function process() {
        $dom = new \DOMDocument(version: '1.0', encoding: 'UTF-8');
        $references = file(filename: 'examples/ayana/15096/ayana.15096.txt', flags: FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Procesar cada referencia
        foreach ($references as $index => $ref) {

            $reference = new Reference(plainTextReference: $ref);
            $jats = new JATSReference(reference: $reference,dom: $dom, id: $index);
            $jatsRefList[] = $jats;

            $doi = $jats->getDoi();
            if($doi) {
                $this->oam->addDoi($doi);
                $this->jatsRefListWhithDoi[$doi] = $jats;

            }
        }
        
    }

    public function processDoi(){
        $request = $this->oam->request();
    }

}


$manager = new ReferencesManager();
$manager->process();
$manager->processDoi();