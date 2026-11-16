<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/../ReferenceStrategyInterface.php';
require_once __DIR__ . '/AuthorAnalyzer/IeeeAuthorAnalyzer.php';
require_once __DIR__ . '/DateAnalyzer/IeeeDateAnalyzer.php';
require_once __DIR__ . '/TitleAnalyzer/IeeeTitleAnalyzer.php';
require_once __DIR__ . '/UrlAnalyzer/IeeeUrlAnalyzer.php';

class IeeeReferenceStrategy implements ReferenceStrategyInterface {

    private IeeeAuthorAnalyzer $authorAnalyzer;
    private IeeeDateAnalyzer $dateAnalyzer;
    private IeeeUrlAnalyzer $urlAnalyzer;
    private IeeeTitleAnalyzer $titleAnalyzer;

    public function __construct() {
        $this->authorAnalyzer = new IeeeAuthorAnalyzer();
        $this->dateAnalyzer = new IeeeDateAnalyzer();
        $this->urlAnalyzer = new IeeeUrlAnalyzer();
        $this->titleAnalyzer = new IeeeTitleAnalyzer();
    }

    /**
     * Parses an IEEE reference string into normalized components for JATS XML printers.
     *
     * @param string $plainTextReference
     * @return array
     */
    public function parse(string $plainTextReference): array {
        // 1. Strip citation number prefix [X]
        $cleanText = $this->titleAnalyzer->stripCitationNumber($plainTextReference);

        // 2. Extract DOI and URLs
        $urlResult = $this->urlAnalyzer->analyze($cleanText);
        $urlData = $urlResult['value'];
        $urlType = $urlResult['expression'];

        // Clean text by stripping trailing DOI and URL to avoid parsing interference
        $cleanText = $this->urlAnalyzer->stripIdentifiers($cleanText);

        // 3. Extract Year and Month
        $dateResult = $this->dateAnalyzer->analyze($cleanText);
        $dateData = $dateResult['value'];
        $dateType = $dateResult['expression'];

        // 4. Extract Authors and Title depending on quotation marks
        if (preg_match(IeeeTitlePatterns::TITLE_QUOTED_REGEX, $cleanText, $titleMatches, PREG_OFFSET_CAPTURE)) {
            $rawTitle = trim($titleMatches[1][0], " \t\n\r,.");
            $offsetQuoteStart = $titleMatches[0][1];
            $quoteFullMatchLen = strlen($titleMatches[0][0]);

            $authorsText = trim(substr($cleanText, 0, $offsetQuoteStart), " \t\n\r,.");
            $containerText = trim(substr($cleanText, $offsetQuoteStart + $quoteFullMatchLen), " \t\n\r,.");

            $authorResult = $this->authorAnalyzer->analyze($authorsText);
            $authors = $authorResult['value'];
            $authorType = $authorResult['expression'];

            $pubType = $this->titleAnalyzer->detectPublicationType($containerText, true);
            $titleData = $this->titleAnalyzer->parseQuotedContainer($rawTitle, $containerText, $pubType);
        } else {
            // No quotation marks: typically books, manuals, or standard/thesis references
            $pubType = $this->titleAnalyzer->detectPublicationType($cleanText, false);

            // Split authors from content in unquoted references
            $authorsText = '';
            $restText = $cleanText;

            if (preg_match('/^(.+?(?:and|y|&)\s+[A-ZÁÉÍÓÚ]\.[\s\-–]*[\p{L}\s\.\-]+?),\s*([A-ZÁÉÍÓÚ].+)$/u', $cleanText, $m)) {
                $authorsText = trim($m[1], " \t\n\r,.");
                $restText = $m[2];
            } elseif (preg_match('/^([A-ZÁÉÍÓÚ]\.[\s\-–]*[\p{L}\s\.\-]+?),\s*([A-ZÁÉÍÓÚ].+)$/u', $cleanText, $m)) {
                $authorsText = trim($m[1], " \t\n\r,.");
                $restText = $m[2];
            }

            $authorResult = $this->authorAnalyzer->analyze($authorsText);
            $authors = $authorResult['value'];
            $authorType = $authorResult['expression'];

            $titleData = $this->titleAnalyzer->parseUnquotedContent($restText, $pubType);
        }

        return [
            'author' => $authors,
            'authorType' => $authorType,
            'date' => $dateData,
            'dateType' => $dateType,
            'url' => $urlData,
            'urlType' => $urlType,
            'title' => $titleData,
            'titleType' => $pubType,
            'type' => $pubType,
        ];
    }
}
