<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

/**
 * GenericOpenAlexEnricher
 *
 * Estrategia de respaldo (Fallback Graceful) para recursos de OpenAlex
 * cuyos tipos no tengan una estrategia especializada registrada.
 * Asegura el enriquecimiento con los datos comunes (autores, fechas, DOI, enlaces)
 * y campos genéricos sin arrojar errores ni interrumpir la conversión JATS.
 */
class GenericOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);

        // 1. Título genérico
        $title = $data['title'] ?? null;
        if (!empty($title)) {
            $elements[] = $this->createElement($dom, 'article-title', $title);
        }

        // 2. Fuente genérica si existe
        $sourceName = $data['primary_location']['source']['display_name'] ?? null;
        if (!empty($sourceName)) {
            $elements[] = $this->createElement($dom, 'source', $sourceName);
        }

        return $elements;
    }
}
