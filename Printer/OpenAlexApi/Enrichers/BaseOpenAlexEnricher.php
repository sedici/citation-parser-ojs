<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

require_once __DIR__ . '/OpenAlexEnricherInterface.php';

abstract class BaseOpenAlexEnricher implements OpenAlexEnricherInterface {

    /**
     * Create an XML element if value is not empty.
     */
    protected function createElement(\DOMDocument $dom, string $name, ?string $value): ?\DOMElement {
        if ($value === null || trim($value) === '') {
            return null;
        }
        return $dom->createElement($name, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
    }

    /**
     * Build <person-group person-group-type="author"> from formatted authors array.
     */
    protected function createAuthorsElement(\DOMDocument $dom, array $authorsFormatted): ?\DOMElement {
        if (empty($authorsFormatted)) {
            return null;
        }

        $personGroup = $dom->createElement('person-group');
        $personGroup->setAttribute('person-group-type', 'author');

        foreach ($authorsFormatted as $author) {
            $surname = $author['surname'] ?? null;
            $given = $author['given-names'] ?? null;
            if (!$surname) {
                continue;
            }

            $name = $dom->createElement('name');
            $surnameEl = $dom->createElement('surname', htmlspecialchars($surname, ENT_XML1, 'UTF-8'));
            $name->appendChild($surnameEl);

            if (!empty($given)) {
                $givenEl = $dom->createElement('given-names', htmlspecialchars($given, ENT_XML1, 'UTF-8'));
                $name->appendChild($givenEl);
            }
            $personGroup->appendChild($name);
        }

        return $personGroup;
    }

    /**
     * Parse YYYY-MM-DD into year, month, day components.
     */
    protected function parsePublicationDate(?string $publicationDate): array {
        $year = $month = $day = null;
        if ($publicationDate) {
            $parts = explode('-', $publicationDate);
            $year = $parts[0] ?? null;
            $month = isset($parts[1]) ? (string)(int)$parts[1] : null;
            $day = isset($parts[2]) ? (string)(int)$parts[2] : null;
        }
        return ['year' => $year, 'month' => $month, 'day' => $day];
    }

    /**
     * Create JATS standard <pub-id pub-id-type="doi"> element.
     */
    protected function createDoiElement(\DOMDocument $dom, ?string $doi): ?\DOMElement {
        if (!$doi) {
            return null;
        }
        // Normalize DOI string (remove URL prefix if present)
        $cleanDoi = preg_replace('~^https?://(dx\.)?doi\.org/~i', '', trim($doi));
        if ($cleanDoi === '') {
            return null;
        }
        $pubId = $dom->createElement('pub-id', htmlspecialchars($cleanDoi, ENT_XML1, 'UTF-8'));
        $pubId->setAttribute('pub-id-type', 'doi');
        return $pubId;
    }

    /**
     * Create <ext-link ext-link-type="uri" xlink:href="..."> element.
     */
    protected function createExtLinkElement(\DOMDocument $dom, ?string $url): ?\DOMElement {
        if (!$url || trim($url) === '') {
            return null;
        }
        $cleanUrl = trim($url);
        $ext = $dom->createElement('ext-link', htmlspecialchars($cleanUrl, ENT_XML1, 'UTF-8'));
        $ext->setAttribute('ext-link-type', 'uri');
        $ext->setAttribute('xlink:href', $cleanUrl);
        return $ext;
    }

    /**
     * Base implementation providing common fields across all source types.
     */
    public function enrich(\DOMDocument $dom, array $data): array {
        $elements = [];

        // 1. Person group (authors)
        if (!empty($data['__authors_formatted'])) {
            $authorsEl = $this->createAuthorsElement($dom, $data['__authors_formatted']);
            if ($authorsEl) {
                $elements[] = $authorsEl;
            }
        }

        // 2. Publication date (year, month, day)
        $dateParts = $this->parsePublicationDate($data['publication_date'] ?? null);
        foreach (['year', 'month', 'day'] as $field) {
            if (!empty($dateParts[$field])) {
                $elements[] = $this->createElement($dom, $field, $dateParts[$field]);
            }
        }

        // 3. Article title
        $title = $data['title'] ?? null;
        if ($title) {
            $elements[] = $this->createElement($dom, 'article-title', $title);
        }

        // 4. Source (journal, repository or publisher name)
        $source = $data['primary_location']['source']['display_name'] ?? null;
        if ($source) {
            $elements[] = $this->createElement($dom, 'source', $source);
        }

        // 5. DOI (pub-id pub-id-type="doi")
        $doiEl = $this->createDoiElement($dom, $data['doi'] ?? null);
        if ($doiEl) {
            $elements[] = $doiEl;
        }

        // 6. External link
        $url = $data['primary_location']['landing_page_url'] ?? $data['doi'] ?? null;
        $extLinkEl = $this->createExtLinkElement($dom, $url);
        if ($extLinkEl) {
            $elements[] = $extLinkEl;
        }

        return $elements;
    }
}
