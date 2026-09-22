<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

/**
 * ChapterOpenAlexEnricher
 *
 * Estrategia de enriquecimiento para capítulos de libros (publication-type="chapter").
 * En JATS XML para capítulos:
 * - El título del capítulo se ubica en <chapter-title>.
 * - El título del libro contenedor se ubica en <source>.
 * - La editorial se ubica en <publisher-name>.
 * - Las páginas inicial y final en <fpage> y <lpage>.
 */
class ChapterOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);

        // 1. Título del capítulo
        $chapterTitle = $data['title'] ?? null;
        if (!empty($chapterTitle)) {
            $elements[] = $this->createElement($dom, 'chapter-title', $chapterTitle);
        }

        // 2. Libro contenedor (<source>)
        $bookTitle = $data['primary_location']['source']['display_name'] ?? null;
        if (!empty($bookTitle)) {
            $elements[] = $this->createElement($dom, 'source', $bookTitle);
        }

        // 3. Editorial (<publisher-name>)
        $publisher = $data['primary_location']['source']['host_organization_name']
                     ?? $data['host_venue']['publisher']
                     ?? null;
        if (!empty($publisher)) {
            $elements[] = $this->createElement($dom, 'publisher-name', $publisher);
        }

        // 4. Páginas
        $fpage = $data['biblio']['first_page'] ?? null;
        if (!empty($fpage)) {
            $elements[] = $this->createElement($dom, 'fpage', (string)$fpage);
        }

        $lpage = $data['biblio']['last_page'] ?? null;
        if (!empty($lpage)) {
            $elements[] = $this->createElement($dom, 'lpage', (string)$lpage);
        }

        return $elements;
    }
}
