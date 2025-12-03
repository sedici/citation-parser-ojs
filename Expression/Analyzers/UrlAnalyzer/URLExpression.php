<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/
include_once __DIR__ . '/../Expression.php';
include_once 'UrlAnalyzer.php';

class URLExpression extends Expression {
    
    /**
     * Parses a URL reference string using UrlAnalyzer.
     * @param string $reference The reference string to parse.
     * @return array Parsed components of the reference.
     */
    public static function parse(string $reference): array {
        $analyzer = new UrlAnalyzer();
        return $analyzer->analyze($reference);
    }
}