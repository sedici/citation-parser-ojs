<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
class OpenAlexAPI {
    private string $institutionsUrl = "https://api.openalex.org/institutions?search=";
    private string $worksUrl = "https://api.openalex.org/works/";
    private string $doisUrl = "https://api.openalex.org/works?filter=doi:";


    public function searchInstitutions(string $query): array {
        $url = $this->institutionsUrl . urlencode($query);
        $response = file_get_contents($url);
        if ($response === FALSE) {
            return ["error" => "No se pudo conectar a la API"];
        }

        $data = json_decode($response, true);

        if (isset($data['meta']['count']) && $data['meta']['count'] > 0 && isset($data['results'][0])) {
            return [
                "id" => $data['results'][0]['id'] ?? null,
                "ror" => $data['results'][0]['ror'] ?? null,
                "display_name" => $data['results'][0]['display_name'] ?? null,
                "type" => $data['results'][0]['type'] ?? null
            ];
        }

        return ["error" => "No se encontraron instituciones con el término de búsqueda proporcionado"];
    }

    public function searchWorksWhitDoi(string $query): array {
    
        $url = $this->worksUrl . urlencode($query);
        $response = file_get_contents($url);
    
        if ($response === FALSE) {
            return ["error" => "No se pudo conectar a la API"];
        }
    
        $data = json_decode($response, true);
        
        $result = $data;
        
            $authors = [];
            foreach ($result['authorships'] as $index => $authorship) {
                $authors[$index]['display_name'] = $authorship['author']['display_name'] ?? null;
                $authors[$index]['orcid']  = $authorship['author']['orcid'] ?? null;
            }
            $result['authors'] =  $authors;

            return [
                "title" => $result['title'] ?? null,
                "publication_year" => $result['publication_year'] ?? null,
                "publication_date" => $result['publication_date'] ?? null,
                "source_display_name" => $result['primary_location']['source']['display_name'] ?? null,
                "source_issn_l" => $result['primary_location']['source']['issn_l'] ?? null,
                "authors" => $result['authors'] ?? null,
            ];
    }
    
    public function searchWorksListWithDoi(array $dois) {
        if (empty($dois)) {
            throw new InvalidArgumentException("La lista de DOIs no puede estar vacía.");
        }

        $doiList = implode('|', $dois);        
        $url = $this->doisUrl . urlencode($doiList);
        $response = file_get_contents($url);
        return $response;
    }
}
?>
