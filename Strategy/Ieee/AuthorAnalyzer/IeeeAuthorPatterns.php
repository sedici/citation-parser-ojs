<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

class IeeeAuthorPatterns {

    /**
     * Pattern matching initials preceding surname (e.g. "J. K. Author", "C. B. Hodges", "M. J. S. da Silva")
     */
    public const INITIALS_SURNAME_REGEX = '/^((?:[A-ZÁÉÍÓÚ]\.[\s\-–]*)+)\s+(.+)$/u';

    /**
     * Pattern normalizing author list conjunctions (and, y, e, &) to commas
     */
    public const CONJUNCTIONS_REGEX = '/\s*(?:,\s*)?(?:and|y|e|&)\s+/iu';

    /**
     * Pattern matching 'et al.' suffix
     */
    public const ET_AL_REGEX = '/\b(?:et\s+al\.?)\b/i';

    /**
     * Keywords identifying institutional/corporate authors
     */
    public const INSTITUTION_KEYWORDS = [
        'universidad', 'university', 'instituto', 'institute', 'department',
        'departamento', 'ministerio', 'ministry', 'secretar[ií]a', 'comisi[oó]n',
        'corporation', 'corp', 'inc', 'association', 'asociaci[oó]n', 'ieee',
        'astm', 'ansi', 'iso', 'unesco', 'who', 'oas', 'center', 'centre',
        'grupo', 'group', 'diretrizes', 'decreto', 'lei nº'
    ];

    public static function getInstitutionPattern(): string {
        return '/\b(' . implode('|', self::INSTITUTION_KEYWORDS) . ')\b/iu';
    }
}
