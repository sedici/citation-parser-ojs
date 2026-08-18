<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

class JournalOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        // Obtenemos los elementos base comunes (autores, fecha, título, fuente, doi, ext-link)
        $elements = parent::enrich($dom, $data);

        // Agregamos campos específicos de revistas (journal)
        $biblio = $data['biblio'] ?? [];
        $volume = $biblio['volume'] ?? null;
        $issue = $biblio['issue'] ?? null;
        $firstPage = $biblio['first_page'] ?? null;
        $lastPage = $biblio['last_page'] ?? null;

        if ($volume) {
            $elements[] = $this->createElement($dom, 'volume', $volume);
        }

        if ($issue) {
            $elements[] = $this->createElement($dom, 'issue', $issue);
        }

        // Manejo inteligente de páginas vs. identificadores electrónicos (elocation-id)
        if ($firstPage) {
            // Si empieza con 'e' seguido de números (ej. e0266781), es un elocation-id
            if (preg_match('~^e\d+$~i', trim($firstPage))) {
                $elements[] = $this->createElement($dom, 'elocation-id', $firstPage);
            } else {
                $elements[] = $this->createElement($dom, 'fpage', $firstPage);
                if ($lastPage && $lastPage !== $firstPage) {
                    $elements[] = $this->createElement($dom, 'lpage', $lastPage);
                }
            }
        }

        // ISSN de la revista (tomamos el primero de la lista si existe)
        $issnList = $data['primary_location']['source']['issn'] ?? [];
        if (!empty($issnList) && is_array($issnList)) {
            $primaryIssn = $issnList[0] ?? null;
            if ($primaryIssn) {
                $elements[] = $this->createElement($dom, 'issn', $primaryIssn);
            }
        }

        return $elements;
    }
}
