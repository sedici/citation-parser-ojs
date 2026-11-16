<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/IeeeTitlePatterns.php';
require_once __DIR__ . '/../DateAnalyzer/IeeeDatePatterns.php';

class IeeeTitleAnalyzer {

    /**
     * Strips leading citation numbering prefix [X].
     */
    public function stripCitationNumber(string $text): string {
        return trim(preg_replace(IeeeTitlePatterns::CITATION_NUMBER_REGEX, '', $text));
    }

    /**
     * Detects publication type from container or reference text.
     */
    public function detectPublicationType(string $text, bool $hasQuotes): string {
        if (preg_match(IeeeTitlePatterns::TYPE_CONFERENCE_REGEX, $text)) {
            return 'confproc';
        }
        if (preg_match(IeeeTitlePatterns::TYPE_THESIS_REGEX, $text)) {
            return 'thesis';
        }

        if ($hasQuotes) {
            if (preg_match(IeeeTitlePatterns::CHAPTER_INDICATOR, $text) ||
                (preg_match('/^(?:in|en)\s+/i', $text) && preg_match(IeeeTitlePatterns::BOOK_PUBLISHER_REGEX, $text))) {
                return 'chapter';
            }
            return 'journal';
        }

        // Without quotes, if it has publisher pattern or edition, it is a book
        if (preg_match(IeeeTitlePatterns::BOOK_PUBLISHER_REGEX, $text) || preg_match(IeeeTitlePatterns::EDITION_REGEX, $text)) {
            return 'book';
        }

        // Check if online resource without quotes
        if (preg_match('/\[(?:Online|En\s*l[ií]nea)\]|https?:\/\//i', $text)) {
            return 'webpage';
        }

        return 'book';
    }

    /**
     * Parses container metadata (journal, conference, etc.) when title is quoted.
     */
    public function parseQuotedContainer(string $rawTitle, string $containerText, string $pubType): array {
        $volume = '';
        if (preg_match(IeeeTitlePatterns::VOLUME_REGEX, $containerText, $m)) {
            $volume = $m[1];
        }

        $issue = '';
        if (preg_match(IeeeTitlePatterns::ISSUE_REGEX, $containerText, $m)) {
            $issue = $m[1];
        }

        $fpage = '';
        $lpage = '';
        $pages = '';
        if (preg_match(IeeeTitlePatterns::PAGES_REGEX, $containerText, $m)) {
            $fpage = $m[1];
            $lpage = isset($m[2]) && !empty($m[2]) ? $m[2] : $m[1];
            $pages = $fpage . (!empty($m[2]) ? '-' . $m[2] : '');
        }

        $elocationId = '';
        if (preg_match(IeeeTitlePatterns::ELOCATION_REGEX, $containerText, $m)) {
            $elocationId = $m[1];
        }

        $publisher = '';
        if (preg_match(IeeeTitlePatterns::BOOK_PUBLISHER_REGEX, $containerText, $pm)) {
            $publisher = trim($pm[2]);
        }

        // Extract container / source name: everything before volume, issue, pages, or year
        $source = $this->extractSourceFromContainer($containerText);

        return [
            'title' => $rawTitle,
            'titulo' => $rawTitle,
            'article-title' => $rawTitle,
            'article_title' => $rawTitle,
            'source' => $source,
            'revista' => $source,
            'book' => $source,
            'publishername' => $publisher ?: $source,
            'publisher-name' => $publisher ?: $source,
            'comment' => $source,
            'volumen' => $volume,
            'volume' => $volume,
            'nedicio' => $issue,
            'issue' => $issue,
            'nedition' => '',
            'editorial' => $publisher,
            'paginas' => $pages,
            'pages' => $pages,
            'fpage' => $fpage,
            'lpage' => $lpage,
            'elocation-id' => $elocationId,
        ];
    }

    /**
     * Extracts source/container name before volume, issue, pages, edition, year, or month.
     */
    public function extractSourceFromContainer(string $containerText): string {
        $source = preg_replace('/^(?:in|en)\s+/iu', '', $containerText);

        $monthPatterns = substr(IeeeDatePatterns::MONTH_REGEX, 1, -2);
        $parts = preg_split('/,\s*(?:vol\.|no\.|issue|pp?\.|\b\d+(?:st|nd|rd|th)?\s+ed|\b(?:19\d{2}|20\d{2})\b|' . $monthPatterns . ')/iu', $source);
        $candidate = trim($parts[0] ?? $source, " \t\n\r,.");

        return $candidate;
    }

    /**
     * Parses unquoted references (books, theses, etc.).
     */
    public function parseUnquotedContent(string $restText, string $pubType): array {
        // Handle Thesis references
        if ($pubType === 'thesis' && preg_match(IeeeTitlePatterns::THESIS_DEGREE_REGEX, $restText, $tm, PREG_OFFSET_CAPTURE)) {
            $rawTitle = trim(substr($restText, 0, $tm[0][1]), " \t\n\r,.");
            $degree = $tm[1][0];
            $afterDegree = trim(substr($restText, $tm[0][1] + strlen($tm[0][0])), " \t\n\r,.");
            $institution = preg_replace('/,\s*(?:19\d{2}|20\d{2})\b.*/', '', $afterDegree);
            $institution = trim($institution, " \t\n\r,.");

            return [
                'title' => $rawTitle,
                'titulo' => $rawTitle,
                'source' => $rawTitle,
                'comment' => '[' . $degree . ']',
                'publisher-name' => $institution,
                'publishername' => $institution,
                'publisher-loc' => '',
            ];
        }

        // Default: Book parsing
        $edition = '';
        $editionNum = '';
        if (preg_match(IeeeTitlePatterns::EDITION_REGEX, $restText, $em)) {
            $edition = $em[0];
            $editionNum = $em[1];
        }

        $publisher = '';
        $city = '';
        $titleEnd = strlen($restText);
        if (preg_match(IeeeTitlePatterns::BOOK_PUBLISHER_REGEX, $restText, $pm, PREG_OFFSET_CAPTURE)) {
            $city = trim($pm[1][0]);
            $publisher = trim($pm[2][0]);
            $titleEnd = $pm[0][1];
        }

        $rawTitle = substr($restText, 0, $titleEnd);
        if ($edition) {
            $rawTitle = preg_replace('/,\s*' . preg_quote($edition, '/') . '/i', '', $rawTitle);
        }
        $cleanTitle = trim($rawTitle, " \t\n\r,.");

        return [
            'title' => $cleanTitle,
            'titulo' => $cleanTitle,
            'source' => $cleanTitle,
            'editorial' => $publisher,
            'publisher-name' => $publisher,
            'edicion' => $edition,
            'nedicion' => $editionNum,
            'publisher-loc' => $city,
            'volumen' => '',
            'paginas' => '',
        ];
    }
}
