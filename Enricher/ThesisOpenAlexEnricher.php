<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

/**
 * ThesisOpenAlexEnricher
 *
 * Estrategia de enriquecimiento para tesis y disertaciones (publication-type="thesis").
 * Mapea:
 * - <article-title> (título de la tesis)
 * - <publisher-name> (universidad o institución que otorga el grado)
 * - <comment> [Tesis]
 */
class ThesisOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);

        // 1. Título de la tesis
        $title = $data['title'] ?? null;
        if (!empty($title)) {
            $elements[] = $this->createElement($dom, 'article-title', $title);
        }

        // 2. Institución o Universidad
        $institution = $data['authorships'][0]['institutions'][0]['display_name']
                       ?? $data['primary_location']['source']['host_organization_name']
                       ?? null;
        if (!empty($institution)) {
            $elements[] = $this->createElement($dom, 'publisher-name', $institution);
        }

        // 3. Comentario de tipo de documento
        $elements[] = $this->createElement($dom, 'comment', '[Tesis]');

        return $elements;
    }
}
