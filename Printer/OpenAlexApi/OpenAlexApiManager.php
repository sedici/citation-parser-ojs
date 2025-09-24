<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once 'OpenAlexApi.php';
class OpenAlexApiManager {
    
    private array $dois;
    private array $institutions;
    private $worksWithDoiResponse;
    private $institutionsResponse;
    private OpenAlexAPI $api;

    public function __construct(array $dois = [], array $institutions = []) {
        $this->dois = $dois;
        $this->institutions = $institutions;
        $this->worksWithDoiResponse = null;
        $this->institutionsResponse = null;
        $this->api = new OpenAlexAPI();
    }

    public function addDoi(string $doi): void {
        if (!in_array($doi, $this->dois)) { // Evitar duplicados
            $this->dois[] = $doi;
        }
    }

    public function addInstitution(string $institution): void {
        if (!in_array($institution, $this->institutions)) { // Evitar duplicados
            $this->institutions[] = $institution;
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

    public function removeInstitution(string $institution): bool {
        $index = array_search($institution, $this->institutions);
        if ($index !== false) {
            unset($this->institutions[$index]);
            $this->institutions = array_values($this->institutions); // Reindexar el array
            return true; // Indica que la institución fue eliminada
        }
        return false; // Indica que la institución no estaba en el array
    }

    public function getInstitutions(): array {
        return $this->institutions;
    }

    public function getDois(): array {
        return $this->dois;
    }

    public function doiRequest() {
        try {
            $this->worksWithDoiResponse = $this->api->searchWorksListWithDoi($this->dois);
            return $this->worksWithDoiResponse;
        } catch (Exception $e) {
            error_log("Error al realizar la solicitud a la API: " . $e->getMessage());
            return null;
        }
    }

    public function searchInstitution(String $institution) {
        try {
            if ($institution) {
                $this->institutionsResponse = $this->api->searchWorksListWithInstitutions($institution);
            }

            file_put_contents(
                __DIR__ . '/testInstitution.log',
                print_r($this->institutionsResponse, true)
            );

            return json_decode($this->institutionsResponse, true);
        } catch (Exception $e) {
            error_log("Error al realizar la solicitud a la API: " . $e->getMessage());
            return null;
        }
    }

    public function getWorksWithDoiResponse() {
        return $this->worksWithDoiResponse;
    }
}
/*
$manager = new OpenAlexApiManager();
$manager->addDoi('https://doi.org/10.24215/23143738e136'); 
$manager->addDoi('https://doi.org/10.1371/journal.pone.0266781'); 
$responce = $manager->request();
print_r($responce); */