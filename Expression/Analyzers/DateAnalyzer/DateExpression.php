<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once __DIR__ . '/../Expression.php';
include_once 'DateAnalyzer.php';

class DateExpression extends Expression {

    /**
     * Parse a date expression from the given text.
     *
     * @param string $text The text to parse.
     * @return array The parsed date components.
     */
    public static function parse($text) {
    
        $analyzer = new DateAnalyzer();
        return $analyzer->analyze($text);
    
    }
}

