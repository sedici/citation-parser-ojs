<?php
// tests/src/OpenAlexApiExtended.php

require_once dirname(dirname(__DIR__)) . '/Printer/OpenAlexApi/OpenAlexApi.php';

class OpenAlexApiExtended extends OpenAlexApi {
    public function searchWorks(string $query): array {
        // Use the search parameter for works
        $url = "https://api.openalex.org/works?search=" . urlencode($query);
        
        // Add a polite user agent
        $options = [
            "http" => [
                "header" => "User-Agent: OJS-DocxConverter/1.0 (mailto:admin@example.com)"
            ]
        ];
        $context = stream_context_create($options);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            return ["error" => "No se pudo conectar a la API"];
        }
    
        $data = json_decode($response, true);

        if (isset($data['results']) && count($data['results']) > 0) {
            $first = $data['results'][0];
            
            // Extract relevant fields
            $authors = [];
            if (isset($first['authorships'])) {
                foreach ($first['authorships'] as $index => $authorship) {
                    $authors[$index]['display_name'] = $authorship['author']['display_name'] ?? null;
                    $authors[$index]['orcid']  = $authorship['author']['orcid'] ?? null;
                }
            }

            return [
                "title" => $first['title'] ?? null,
                "publication_year" => $first['publication_year'] ?? null,
                "publication_date" => $first['publication_date'] ?? null,
                "source_display_name" => $first['primary_location']['source']['display_name'] ?? null,
                "source_issn_l" => $first['primary_location']['source']['issn_l'] ?? null,
                "authors" => $authors,
                "doi" => $first['doi'] ?? null,
                "type" => $first['type'] ?? null,
                "volume" => $first['biblio']['volume'] ?? null,
                "issue" => $first['biblio']['issue'] ?? null,
                "first_page" => $first['biblio']['first_page'] ?? null,
                "last_page" => $first['biblio']['last_page'] ?? null,
                "relevance_score" => $first['relevance_score'] ?? 0
            ];
        } else {
             return ["error" => "No se encontraron coincidencias"];
        }
    }
}
