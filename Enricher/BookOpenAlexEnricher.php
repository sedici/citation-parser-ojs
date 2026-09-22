<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

/**
 * BookOpenAlexEnricher
 *
 * Estrategia de enriquecimiento para libros completos (publication-type="book").
 * En JATS XML para libros:
 * - El título del libro se ubica en <source> (Texture no admite <article-title> en libros).
 * - La editorial se ubica en <publisher-name>.
 * - La edición se ubica en <edition> si está disponible.
 */
class BookOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);

        // 1. Título del libro (en JATS va en <source>)
        $title = $data['title'] ?? null;
        if (!empty($title)) {
            $elements[] = $this->createElement($dom, 'source', $title);
        }

        // 2. Editorial (<publisher-name>)
        $publisher = $data['primary_location']['source']['host_organization_name']
                     ?? $data['host_venue']['publisher']
                     ?? null;
        if (!empty($publisher)) {
            $elements[] = $this->createElement($dom, 'publisher-name', $publisher);
        }

        // 3. Edición (si viniera en la información bibliográfica)
        $edition = $data['biblio']['issue'] ?? null;
        if (!empty($edition) && is_string($edition) && stripos($edition, 'ed') !== false) {
            $elements[] = $this->createElement($dom, 'edition', $edition);
        }

        return $elements;
    }
}
