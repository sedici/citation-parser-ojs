<?php

/* 
 * Copyright (C) 2025-2026 PREBI-SEDICI, Universidad Nacional de La Plata
 * Licensed under GPLv3: see LICENSE file for details.
 */

require_once __DIR__ . '/IeeeDatePatterns.php';

class IeeeDateAnalyzer {

    /**
     * Extracts date components (year and month) from reference text.
     *
     * @param string $text
     * @return array ['expression' => 'year'|null, 'value' => ['year' => string, 'month' => string]]
     */
    public function analyze(string $text): array {
        $year = '';
        if (preg_match_all(IeeeDatePatterns::YEAR_REGEX, $text, $m)) {
            // In IEEE references, publication year typically appears near the end
            $year = (string)end($m[1]);
        }

        $month = '';
        if (preg_match(IeeeDatePatterns::MONTH_REGEX, $text, $m)) {
            $monthKey = strtolower(rtrim($m[1], '.'));
            if (isset(IeeeDatePatterns::MONTH_MAP[$monthKey])) {
                $month = (string)IeeeDatePatterns::MONTH_MAP[$monthKey];
            }
        }

        return [
            'expression' => !empty($year) ? 'year' : null,
            'value' => [
                'year' => $year,
                'month' => $month,
            ]
        ];
    }
}
