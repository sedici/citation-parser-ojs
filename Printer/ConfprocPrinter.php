<?php
include_once 'TitlePrinter.php';
include_once 'OpenAlexApi/EnrichmentInstitutionInterface.php';
include_once 'OpenAlexApi/OpenAlexApi.php';

class ConfprocPrinter extends TitlePrinter implements EnrichmentInstitutionInterface{

    public function toPlainText(): string{
        //'Apellido Autor, N. N. (10 de abril de 2023). Título del trabajo. (3ª ed., Vol. 4). Editorial.';
        return $this->reference['book'].' ('.$this->reference['edicion'].'). '.$this->reference['editorial'].'.';
    }

    public function getPublisherName(): string{
        return $this->get('publishername');
    }

    public function getTitle(): string{
        return $this->get('title');
    }

    public function getSource(): string{
        return $this->get('title');
    }

    public function getPublisherLoc(): string{
        return $this->get('publisherloc');
    }

    public function getEvent(): string{
        return $this->get('event');
    }

    public function getComment(): string{
        return $this->get('comment');
    }

    public function createXMLElements(): array {
        $elements = [];

        $sourceElement = $this->createElement('article-title',$this->getSource());
        $elements[] = $sourceElement;

        $commentElement = $this->createElement('comment',$this->getComment());
        $elements[] = $commentElement;

        $publisherLocElement = $this->createElement('conf-loc',$this->getPublisherLoc());
        $elements[] = $publisherLocElement;

        $publisherName = $this->getPublisherName();
        if (!empty($publisherName)) {
            $publisherNameElement = $this->createElement('conf-name',$publisherName);
            $elements[] = $publisherNameElement;

            $result = $this->enrichInstitutionData($publisherName);
            if ($result) $elements[] = $result;
        }
        return $elements;
    }


    public function enrichInstitutionData(string $name) {
        try {
            if (empty($name)) return false;
            
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