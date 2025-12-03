<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
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
        if (!in_array($doi, $this->dois)) {
            $this->dois[] = $doi;
        }
    }

    public function removeDoi(string $doi): bool {
        $index = array_search($doi, $this->dois);
        if ($index !== false) {
            unset($this->dois[$index]);
            $this->dois = array_values($this->dois);
            return true;
        }
        return false;
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