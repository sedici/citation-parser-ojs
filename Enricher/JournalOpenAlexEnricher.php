<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

/**
 * JournalOpenAlexEnricher
 *
 * Estrategia de enriquecimiento para artículos de revistas científicas (publication-type="journal").
 * Mapea:
 * - <article-title> (título del artículo)
 * - <source> (nombre de la revista)
 * - <volume>, <issue> (volumen y fascículo)
 * - <fpage>, <lpage> (páginas)
 * - <issn> (ISSN de la revista)
 */
class JournalOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);

        // 1. Título del artículo
        $title = $data['title'] ?? null;
        if (!empty($title)) {
            $elements[] = $this->createElement($dom, 'article-title', $title);
        }

        // 2. Nombre de la revista (<source>)
        $journal = $data['primary_location']['source']['display_name'] ?? null;
        if (!empty($journal)) {
            $elements[] = $this->createElement($dom, 'source', $journal);
        }

        // 3. Volumen y número/fascículo
        $volume = $data['biblio']['volume'] ?? null;
        if (!empty($volume)) {
            $elements[] = $this->createElement($dom, 'volume', (string)$volume);
        }

        $issue = $data['biblio']['issue'] ?? null;
        if (!empty($issue)) {
            $elements[] = $this->createElement($dom, 'issue', (string)$issue);
        }

        // 4. Páginas vs. identificadores electrónicos (elocation-id)
        $fpage = $data['biblio']['first_page'] ?? null;
        $lpage = $data['biblio']['last_page'] ?? null;
        if (!empty($fpage)) {
            if (preg_match('~^e\d+$~i', trim((string)$fpage))) {
                $elements[] = $this->createElement($dom, 'elocation-id', (string)$fpage);
            } else {
                $elements[] = $this->createElement($dom, 'fpage', (string)$fpage);
                if (!empty($lpage) && $lpage !== $fpage) {
                    $elements[] = $this->createElement($dom, 'lpage', (string)$lpage);
                }
            }
        }

        // 5. ISSN de la revista
        $issn = $data['primary_location']['source']['issn_l'] 
                ?? $data['primary_location']['source']['issn'][0] 
                ?? null;
        if (!empty($issn)) {
            $elements[] = $this->createElement($dom, 'issn', (string)$issn);
        }

        return $elements;
    }
}
