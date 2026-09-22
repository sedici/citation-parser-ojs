<?php
/* 
* Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/OpenAlexEnricherInterface.php';

/**
 * BaseOpenAlexEnricher
 *
 * Clase abstracta base que centraliza la extracción y formateo de elementos
 * comunes a cualquier recurso bibliográfico (autores, fechas, DOI estándar y enlaces),
 * aprovechando que el JSON de OpenAlex mantiene una estructura uniforme.
 */
abstract class BaseOpenAlexEnricher implements OpenAlexEnricherInterface {

    /**
     * Construye los elementos XML comunes compartidos por cualquier tipo de fuente:
     * - <person-group person-group-type="author"> con <surname> y <given-names>
     * - <year>, <month>, <day>
     * - <pub-id pub-id-type="doi">
     * - <ext-link ext-link-type="uri">
     *
     * @param \DOMDocument $dom
     * @param array $data
     * @return \DOMElement[]
     */
    protected function buildCommonElements(\DOMDocument $dom, array $data): array {
        $elements = [];

        // 1. Autores (usando la lista preformateada por AuthorValidator)
        $authorsElement = $this->buildAuthorsElement($dom, $data);
        if ($authorsElement !== null) {
            $elements[] = $authorsElement;
        }

        // 2. Fechas de publicación
        $dateElements = $this->buildDateElements($dom, $data);
        foreach ($dateElements as $el) {
            $elements[] = $el;
        }

        // 3. DOI estándar JATS
        $doiElement = $this->buildDoiElement($dom, $data);
        if ($doiElement !== null) {
            $elements[] = $doiElement;
        }

        // 4. Enlace externo URL
        $extLinkElement = $this->buildExtLinkElement($dom, $data);
        if ($extLinkElement !== null) {
            $elements[] = $extLinkElement;
        }

        return $elements;
    }

    /**
     * Construye el nodo <person-group person-group-type="author">.
     */
    protected function buildAuthorsElement(\DOMDocument $dom, array $data): ?\DOMElement {
        $authors = $data['__authors_formatted'] ?? [];
        if (empty($authors)) {
            return null;
        }

        $personGroup = $dom->createElement('person-group');
        $personGroup->setAttribute('person-group-type', 'author');

        foreach ($authors as $author) {
            $surname = $author['surname'] ?? null;
            $given = $author['given-names'] ?? null;

            if (empty($surname)) {
                continue;
            }

            $name = $dom->createElement('name');
            $surnameEl = $dom->createElement('surname');
            $surnameEl->appendChild($dom->createTextNode($surname));
            $name->appendChild($surnameEl);

            if (!empty($given)) {
                $givenEl = $dom->createElement('given-names');
                $givenEl->appendChild($dom->createTextNode($given));
                $name->appendChild($givenEl);
            }

            $personGroup->appendChild($name);
        }

        return $personGroup->hasChildNodes() ? $personGroup : null;
    }

    /**
     * Extrae y genera los nodos <year>, <month>, <day>.
     */
    protected function buildDateElements(\DOMDocument $dom, array $data): array {
        $elements = [];
        $publicationDate = $data['publication_date'] ?? null;
        $publicationYear = $data['publication_year'] ?? null;

        $year = $month = $day = null;

        if (!empty($publicationDate)) {
            $parts = explode('-', $publicationDate);
            $year = !empty($parts[0]) ? (int)$parts[0] : null;
            $month = !empty($parts[1]) ? (int)$parts[1] : null;
            $day = !empty($parts[2]) ? (int)$parts[2] : null;
        } elseif (!empty($publicationYear)) {
            $year = (int)$publicationYear;
        }

        if ($year !== null) {
            $elements[] = $this->createElement($dom, 'year', (string)$year);
        }
        if ($month !== null) {
            $elements[] = $this->createElement($dom, 'month', (string)$month);
        }
        if ($day !== null) {
            $elements[] = $this->createElement($dom, 'day', (string)$day);
        }

        return $elements;
    }

    /**
     * Construye el identificador DOI en formato estándar JATS: <pub-id pub-id-type="doi">.
     */
    protected function buildDoiElement(\DOMDocument $dom, array $data): ?\DOMElement {
        $rawDoi = $data['doi'] ?? $data['ids']['doi'] ?? null;
        if (empty($rawDoi)) {
            return null;
        }

        // Normalizar DOI removiendo prefijo https://doi.org/ o http://dx.doi.org/
        $cleanDoi = preg_replace('/^https?:\/\/(?:dx\.)?doi\.org\//i', '', trim($rawDoi));
        if (empty($cleanDoi)) {
            return null;
        }

        $doiElement = $this->createElement($dom, 'pub-id', $cleanDoi);
        $doiElement->setAttribute('pub-id-type', 'doi');

        return $doiElement;
    }

    /**
     * Construye el elemento <ext-link ext-link-type="uri" xlink:href="...">.
     */
    protected function buildExtLinkElement(\DOMDocument $dom, array $data): ?\DOMElement {
        $landingUrl = $data['primary_location']['landing_page_url'] 
                      ?? $data['primary_location']['pdf_url'] 
                      ?? null;

        if (empty($landingUrl)) {
            return null;
        }

        $extLink = $this->createElement($dom, 'ext-link', $landingUrl);
        $extLink->setAttribute('ext-link-type', 'uri');
        $extLink->setAttribute('xlink:href', $landingUrl);

        return $extLink;
    }

    /**
     * Utilidad para crear elementos DOM de forma segura con contenido textual.
     */
    protected function createElement(\DOMDocument $dom, string $name, ?string $value): \DOMElement {
        $el = $dom->createElement($name);
        if ($value !== null && $value !== '') {
            $el->appendChild($dom->createTextNode($value));
        }
        return $el;
    }
}
