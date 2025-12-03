<?php
/* 
* Copyright (C) 2025 PREBI-SEDICI, Universidad Nacional de La Plata
* Licensed under GPLv3: see LICENSE file for details.
*/

include_once __DIR__ . '/../Expression.php';
include_once 'TitleAnalyzer.php';

class TitleExpression extends Expression{

    /**
     * Parses the given reference to analyze title patterns.
     *
     * @param string $reference The reference text to be analyzed.
     * @param array $types Optional array of specific types of references to analyze.
     * @return array The result of the title analysis.
     */
    public static function parse($reference, array $types = []) {

        $analyzer = new TitleAnalyzer($types);
        return $analyzer->analyze($reference);
    }

}