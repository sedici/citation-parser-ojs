<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/IeeeUrlPatterns.php';

class IeeeUrlAnalyzer {

    /**
     * Extracts DOI and URL data from reference text.
     *
     * @param string $text
     * @return array ['expression' => 'DOI'|'URL'|null, 'value' => array]
     */
    public function analyze(string $text): array {
        $doi = $this->extractDoi($text);
        $url = $this->extractUrl($text, $doi);

        $urlData = [];
        $urlType = null;

        if ($doi) {
            $urlData['doi'] = $doi;
            $urlData['url'] = 'https://doi.org/' . $doi;
            $urlType = 'DOI';
        }

        if ($url) {
            $urlData['url'] = $url;
            if (!$urlType) {
                $urlType = 'URL';
            }
        }

        return [
            'expression' => $urlType,
            'value' => $urlData
        ];
    }

    /**
     * Extracts DOI from text.
     */
    public function extractDoi(string $text): ?string {
        if (preg_match(IeeeUrlPatterns::DOI_REGEX, $text, $m)) {
            return trim($m[1], " \t\n\r,.;");
        }
        return null;
    }

    /**
     * Extracts URL from text.
     */
    public function extractUrl(string $text, ?string $doi = null): ?string {
        if (preg_match(IeeeUrlPatterns::ONLINE_AVAILABLE_REGEX, $text, $m)) {
            return trim($m[1], " \t\n\r,.;");
        }

        if (preg_match_all(IeeeUrlPatterns::GENERIC_URL_REGEX, $text, $matches)) {
            foreach ($matches[0] as $candidate) {
                $candidate = trim($candidate, " \t\n\r,.;");
                // Avoid using doi.org as URL if already captured as DOI
                if ($doi && (stripos($candidate, $doi) !== false || stripos($candidate, 'doi.org') !== false)) {
                    continue;
                }
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Strips DOI and URLs from text to simplify title and author parsing.
     */
    public function stripIdentifiers(string $text): string {
        $text = preg_replace(IeeeUrlPatterns::DOI_REGEX, '', $text);
        $text = preg_replace(IeeeUrlPatterns::ONLINE_AVAILABLE_REGEX, '', $text);
        $text = preg_replace(IeeeUrlPatterns::ONLINE_ANNOTATION_REGEX, '', $text);
        $text = preg_replace(IeeeUrlPatterns::TRAILING_URL_REGEX, '', $text);
        return trim($text, " \t\n\r,.");
    }
}
