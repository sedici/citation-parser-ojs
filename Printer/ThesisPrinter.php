<?php
include_once 'GenericPrinter.php';
include_once 'OpenAlexApi/EnrichmentInstitutionInterface.php';
include_once 'OpenAlexApi/OpenAlexApi.php';

class ThesisPrinter extends TitlePrinter implements EnrichmentInstitutionInterface{

    public function toPlainText(): string{
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return "";
    }

    public function createXMLElements(): array {
        /*
            <publisher-name>Arizona State University</publisher-name>
            <part-title>Part 2, Space medicine</part-title>
            <source>Human factors: aerospace medicine and the origins of manned space flight in the United States</source>
            <comment>[dissertation]</comment>

            <publisher-loc>Santiago, Chile</publisher-loc>
            <publisher-name>Facultad de Medicina, Escuela de Salud pública, Universidad Mayor</publisher-name>
            <comment>
        */

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
            // Llama a la API de OpenAlex para obtener la información
            $api = new OpenAlexAPI();
            $response = $api->searchInstitutions($name);
    
            // Verifica si la respuesta contiene los datos necesarios
            if (!isset($response['ror']) || !isset($response['display_name']) || !isset($response['type'])) {
                // Si la respuesta no tiene los datos necesarios, devuelve false
                return false;
            }
    
            // Si la respuesta es válida, crea los elementos
            $institutionWrap = $this->dom->createElement('institution-wrap');
    
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