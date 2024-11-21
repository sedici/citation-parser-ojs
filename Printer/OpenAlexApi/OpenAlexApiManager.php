<?php
include_once 'OpenAlexApi.php';
class OpenAlexApiManager {
    
    private array $dois;
    private $response;
    private OpenAlexAPI $api;

    public function __construct(array $dois = []){
        $this->dois = $dois;
        $this->response = null;
        $this->api = new OpenAlexAPI();
    }

    public function addDoi(string $doi): void {
        if (!in_array($doi, $this->dois)) { // Evitar duplicados
            $this->dois[] = $doi;
        }
    }

    public function removeDoi(string $doi): bool {
        $index = array_search($doi, $this->dois);
        if ($index !== false) {
            unset($this->dois[$index]);
            $this->dois = array_values($this->dois); // Reindexar el array
            return true; // Indica que el DOI fue eliminado
        }
        return false; // Indica que el DOI no estaba en el array
    }

    public function listDois(): array {
        return $this->dois;
    }

    public function request() {
        try {
            $this->response = $this->api->searchWorksListWithDoi($this->dois);
            return $this->response;
        } catch (Exception $e) {
            error_log("Error al realizar la solicitud a la API: " . $e->getMessage());
            return null;
        }
    }

    public function getResponse() {
        return $this->response;
    }
}
/*
$manager = new OpenAlexApiManager();
$manager->addDoi('https://doi.org/10.24215/23143738e136'); 
$manager->addDoi('https://doi.org/10.1371/journal.pone.0266781'); 
$responce = $manager->request();
print_r($responce); */