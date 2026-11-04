<?php

/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

/**
 * AuthorFullNameProcessor
 *
 * Small, reusable helper to apply a single strategy for:
 * - Matching an OpenAlex display_name against a reference author
 * - Splitting display_name into JATS parts: surname and given-names
 *
 * Strategy used in this class:
 * 1) Verify surname presence (case-insensitive) in the full OpenAlex name.
 * 2) Remove surname from the full name and consider the remainder as candidate given-names.
 * 3) If the reference gives given-name initials, check that each initial matches at least one
 *    token of the remainder. If not, treat as not matched.
 * 4) For output values, prefer:
 *    - surname: the reference surname (normalized), falling back to the chunk found in display_name
 *    - given-names: the remainder from display_name; if empty, fall back to reference given-names
 */
class AuthorFullNameProcessor {

    /**
     * Try to match a reference author against an OpenAlex display_name and return split parts.
     *
     * @param array $referenceAuthor Must contain keys 'apellido' and optionally 'nombres'
     * @param string $openAlexDisplayName Full author name from OpenAlex (e.g., "Jane Q. Doe")
     * @return array|null ['surname' => string, 'given-names' => string] or null if not matched
     */
    public static function matchReferenceToDisplayName(array $referenceAuthor, string $openAlexDisplayName): ?array {
        $expectedSurname = trim((string)($referenceAuthor['apellido'] ?? ''));
        $expectedGivenNames = trim((string)($referenceAuthor['nombres'] ?? ''));

        if ($expectedSurname === '') {
            return null;
        }

        // 1) Check surname presence (case-insensitive)
        $pos = stripos($openAlexDisplayName, $expectedSurname);
        if ($pos === false) {
            return null;
        }

        // Capture the exact surname text as it appears in display_name (preserve case/accents)
        $matchedSurname = substr($openAlexDisplayName, $pos, strlen($expectedSurname));

        // 2) Remove surname from the full name to get candidate given names
        $remaining = trim(self::removeCaseInsensitive($openAlexDisplayName, $expectedSurname));
        $remaining = self::normalizeSpaces($remaining);

        // 3) If we have initials/names in the reference, ensure each initial appears in order.
        //    When there are multiple initials, build given-names from the matched tokens only (skip non-matching tokens).
        //    When there is a single initial, keep the full remaining given-names (e.g., "A." -> "Ana María").
        $matchedTokens = null;
        if ($expectedGivenNames !== '') {
            $initials = self::extractInitials($expectedGivenNames);
            $matchedTokens = self::collectMatchedTokens($remaining, $initials);
            if ($matchedTokens === null) {
                return null;
            }
        }

        // 4) Build output parts
        $outSurname = $expectedSurname !== '' ? $expectedSurname : $matchedSurname;
        if ($matchedTokens !== null) {
            if (count(self::extractInitials($expectedGivenNames)) <= 1) {
                // Single initial: keep all remaining tokens
                $outGiven = $remaining;
            } else {
                // Multiple initials: only matched tokens
                $outGiven = implode(' ', $matchedTokens);
            }
        } else {
            $outGiven = $remaining !== '' ? $remaining : $expectedGivenNames;
        }

        return [
            'surname' => trim($outSurname),
            'given-names' => trim($outGiven),
        ];
    }

    /**
     * Remove first case-insensitive occurrence of $needle in $haystack
     */
    private static function removeCaseInsensitive(string $haystack, string $needle): string {
        $pos = stripos($haystack, $needle);
        if ($pos === false) { return $haystack; }
        return trim(substr($haystack, 0, $pos) . substr($haystack, $pos + strlen($needle)));
    }

    private static function normalizeSpaces(string $s): string {
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    /**
     * Extract initials from a given-names string like "J. P." or "Juan Pablo"
     * -> ["J", "P"]
     */
    private static function extractInitials(string $givenNames): array {
        $parts = preg_split('/\s+/u', trim($givenNames));
        $initials = [];
        foreach ($parts as $part) {
            $p = rtrim($part, '.') ;
            if ($p === '') { continue; }
            $initials[] = mb_substr($p, 0, 1);
        }
        return $initials;
    }

    /**
     * Check that each initial matches a distinct token in order.
     * Example: initials [T, T] vs tokens [Tomas, Nahuel] -> FAIL (second T cannot match Nahuel)
     * Example: initials [J, P] vs tokens [Juan, Pablo] -> OK
     * - Hyphenated tokens are split (e.g., "Jean-Pierre").
     */
    private static function collectMatchedTokens(string $remaining, array $initials): ?array {
        if (empty($initials)) { return []; }

        $tokens = array_values(array_filter(preg_split('/\s+/u', $remaining) ?: [], fn($t) => $t !== ''));
        $startIdx = 0; // enforce order; also remove matched tokens to avoid reuse
        $matchedSeq = [];

        foreach ($initials as $initial) {
            $initial = (string)$initial;
            $matched = false;
            $count = count($tokens);

            for ($i = $startIdx; $i < $count; $i++) {
                $t = $tokens[$i] ?? '';
                if ($t === '') { continue; }

                // consider hyphenated parts
                $parts = preg_split('/-+/u', $t) ?: [$t];
                $parts = array_values(array_filter($parts, fn($p) => $p !== ''));

                $partMatch = false;
                foreach ($parts as $p) {
                    if (stripos($p, $initial) === 0) { $partMatch = true; break; }
                }

                if ($partMatch || stripos($t, $initial) === 0) {
                    // capture matched token and remove to prevent reuse
                    $matchedSeq[] = $t;
                    array_splice($tokens, $i, 1);
                    $matched = true;
                    // next search must start at the same index since items shifted left
                    $startIdx = $i;
                    break;
                }
            }

            if (!$matched) { return null; }
        }

        return $matchedSeq;
    }
}

?>
