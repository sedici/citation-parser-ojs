<?php
include_once 'URLPrinter.php';
class DOIPrinter extends URLPrinter{

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
            
            // Llama a la API de OpenAlex para obtener la información
            $api = new OpenAlexAPI();
            $response = $api->searchWorksWhitDoi($doi);
    
            // Verifica si la respuesta contiene los datos necesarios
            if (!isset($response)) {
                // Si la respuesta no tiene los datos necesarios, devuelve false
                return false;
            }
            
            $enrichDoiData = [];

            // Si la respuesta es válida, crea los elementos
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
            // En caso de error en la llamada a la API, captura la excepción y devuelve false
            return false;
        }
    }
}