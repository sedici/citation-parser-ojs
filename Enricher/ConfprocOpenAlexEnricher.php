<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/BaseOpenAlexEnricher.php';

/**
 * ConfprocOpenAlexEnricher
 *
 * Estrategia de enriquecimiento para ponencias y actas de congresos (publication-type="confproc").
 * Mapea:
 * - <article-title> (título de la ponencia)
 * - <conf-name> o <source> (nombre del congreso o de las actas)
 * - <fpage>, <lpage> (páginas)
 */
class ConfprocOpenAlexEnricher extends BaseOpenAlexEnricher {

    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = $this->buildCommonElements($dom, $data);

        // 1. Título de la ponencia
        $title = $data['title'] ?? null;
        if (!empty($title)) {
            $elements[] = $this->createElement($dom, 'article-title', $title);
        }

        // 2. Nombre del congreso o actas (<conf-name> y <source>)
        $confName = $data['primary_location']['source']['display_name'] ?? null;
        if (!empty($confName)) {
            $elements[] = $this->createElement($dom, 'source', $confName);
        }

        // 3. Páginas
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
