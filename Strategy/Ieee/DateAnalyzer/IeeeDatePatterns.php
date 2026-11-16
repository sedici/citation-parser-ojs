<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

class IeeeDatePatterns {

    /**
     * Pattern matching 4-digit years (1900-2099)
     */
    public const YEAR_REGEX = '/\b(19\d{2}|20\d{2})\b/';

    /**
     * Months mapping (English and Spanish abbreviations and full names to number 1-12)
     */
    public const MONTH_MAP = [
        'jan' => 1, 'january' => 1, 'ene' => 1, 'enero' => 1,
        'feb' => 2, 'february' => 2, 'febrero' => 2,
        'mar' => 3, 'march' => 3, 'marzo' => 3,
        'apr' => 4, 'april' => 4, 'abr' => 4, 'abril' => 4,
        'may' => 5, 'mayo' => 5,
        'jun' => 6, 'june' => 6, 'junio' => 6,
        'jul' => 7, 'july' => 7, 'julio' => 7,
        'aug' => 8, 'august' => 8, 'ago' => 8, 'agosto' => 8,
        'sep' => 9, 'sept' => 9, 'september' => 9, 'set' => 9, 'septiembre' => 9, 'setiembre' => 9,
        'oct' => 10, 'october' => 10, 'octubre' => 10,
        'nov' => 11, 'november' => 11, 'noviembre' => 11,
        'dec' => 12, 'december' => 12, 'dic' => 12, 'diciembre' => 12,
    ];

    /**
     * Pattern matching month names in English and Spanish
     */
    public const MONTH_REGEX = '/\b(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:t|tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?|ene(?:ro)?|feb(?:rero)?|mar(?:zo)?|abr(?:il)?|may(?:o)?|jun(?:io)?|jul(?:io)?|ago(?:sto)?|sep(?:tiembre)?|set(?:iembre)?|oct(?:ubre)?|nov(?:iembre)?|dic(?:iembre)?)\b/i';
}
