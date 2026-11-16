<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/IeeeAuthorPatterns.php';

class IeeeAuthorAnalyzer {

    /**
     * Analyzes and parses the authors string into normalized components.
     *
     * @param string $authorsString
     * @return array ['expression' => 'collab'|'person', 'value' => array]
     */
    public function analyze(string $authorsString): array {
        $authorsString = trim($authorsString, " \t\n\r,.");
        if (empty($authorsString)) {
            return [
                'expression' => null,
                'value' => ['authors' => []]
            ];
        }

        // 1. Institutional author check
        if ($this->isInstitutionalAuthor($authorsString)) {
            return [
                'expression' => 'collab',
                'value' => [
                    'institution' => $authorsString
                ]
            ];
        }

        // 2. Individual authors list
        $normalized = preg_replace(IeeeAuthorPatterns::CONJUNCTIONS_REGEX, ', ', $authorsString);
        $tokens = explode(',', $normalized);

        $authors = [];
        foreach ($tokens as $token) {
            $token = trim($token, " \t\n\r.");
            if (empty($token) || strtolower($token) === 'et al') {
                continue;
            }

            $author = $this->parseSingleAuthor($token);
            if (!empty($author['apellido'])) {
                $authors[] = $author;
            }
        }

        return [
            'expression' => !empty($authors) ? 'person' : null,
            'value' => ['authors' => $authors]
        ];
    }

    /**
     * Parses an individual author string.
     */
    public function parseSingleAuthor(string $name): array {
        $name = trim(preg_replace(IeeeAuthorPatterns::ET_AL_REGEX, '', $name));

        // Pattern 1: Initials preceding surname (e.g. "J. K. Author", "C. B. Hodges", "M. J. S. da Silva")
        if (preg_match(IeeeAuthorPatterns::INITIALS_SURNAME_REGEX, $name, $m)) {
            return [
                'apellido' => trim($m[2]),
                'nombres' => trim($m[1]),
                'role' => 'author',
            ];
        }

        // Pattern 2: First name in full, then surname (e.g. "John Doe" or "Fernanda Appolinário")
        $parts = preg_split('/\s+/u', $name);
        if (count($parts) === 2) {
            return [
                'apellido' => $parts[1],
                'nombres' => $parts[0],
                'role' => 'author',
            ];
        }

        if (count($parts) > 2) {
            $first = array_shift($parts);
            return [
                'apellido' => implode(' ', $parts),
                'nombres' => $first,
                'role' => 'author',
            ];
        }

        // Fallback for single word name
        return [
            'apellido' => $name,
            'nombres' => '',
            'role' => 'author',
        ];
    }

    /**
     * Checks if author name matches institutional patterns.
     */
    public function isInstitutionalAuthor(string $author): bool {
        return (bool)preg_match(IeeeAuthorPatterns::getInstitutionPattern(), $author);
    }
}
